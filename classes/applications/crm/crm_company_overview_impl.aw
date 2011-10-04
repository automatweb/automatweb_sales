<?php

class crm_company_overview_impl extends class_base
{
	function crm_company_overview_impl()
	{
		$this->init();
	}

	function _get_org_actions($arr)
	{
		$this->do_org_actions($arr, array());
	}

	function _get_org_calls($arr)
	{
		$args = array();
		$args["type"] = "RELTYPE_CALL";
		$this->do_org_actions($arr, $args, CL_CRM_CALL);
	}

	function _get_org_meetings($arr)
	{
		$args = array();
		$args["type"] = "RELTYPE_KOHTUMINE";
		$this->do_org_actions($arr, $args, CL_CRM_MEETING);
	}

	function _get_org_tasks($arr)
	{
		$args = array();
		$args["type"] = "RELTYPE_TASK";
		$this->do_org_actions($arr, $args, CL_TASK);
	}

	function get_overview($arr = array())
	{
		return $this->overview;
	}

	function do_org_actions($arr, $args, $clid)
	{
		// whee, this thing includes project and that uses properties, so we gots
		// to do this here or something. damn, we need to do the reltype
		// loading in get_instance or something

		$cfgu = new cfgutils();
		$cfgu->load_class_properties(array(
			"file" => "project",
			"clid" => 239
		));

		$ob = $arr["obj_inst"];
		$conns = $ob->connections_from($args);
		$t = $arr["prop"]["vcl_inst"];

		$arr["prop"]["vcl_inst"]->configure(array(
			"overview_func" => array($this,"get_overview"),
		));

		$p = new planner();
		$cal = $p->get_calendar_for_user();
		if ($cal)
		{
			$calo = obj($cal);
			if (!$arr["request"]["viewtype"])
			{
				$arr["request"]["viewtype"] = $p->viewtypes[$calo->prop("default_view")];
			}

			$wds = safe_array($calo->prop("workdays"));
			$full_weeks = false;
			// if no workdays are defined, use all of them
			for($wd = 1; $wd <= 7; $wd++)
			{
				if(!$wds[$wd])
				{
					$full_weeks = false;
					break;
				}
				else
				{
					$full_weeks = true;
				}
			}
			$arr["prop"]["vcl_inst"]->configure(array(
				"full_weeks" => $full_weeks
			));
		}

		$range = $arr["prop"]["vcl_inst"]->get_range(array(
			"date" => $arr["request"]["date"],
			"viewtype" => !empty($arr["request"]["viewtype"]) ? $arr["request"]["viewtype"] : $arr["prop"]["viewtype"],
		));

		$start = $range["start"];
		$end = $range["end"];

		$overview_start = $range["overview_start"];

		$classes = aw_ini_get("classes");

		$return_url = get_ru();
		$planner = new planner();

		$arr["range"] = $range;
		$task_ol = $this->_get_task_list($arr);
		$evts = $task_ol->arr();

		$this->overview = array();

		// get b-days
		if ($calo && $calo->prop("show_bdays") == 1)
		{
			// if the view company is not the current company, then restrict the people to that company's persons
			$cc = get_current_company();
			$p_id_filt = "";
			if ($arr["obj_inst"]->id() != $cc->id())
			{
				$co_i = new crm_company();
				$epl = new aw_array(array_keys($co_i->get_employee_picker($arr["obj_inst"])));
				$p_id_filt = " AND objects.oid in (".$epl->to_sql().")";
			}
			$s_m = date("m", $start);
			$e_m = date("m", $end);
			$pred = $s_m > $e_m ? "OR" : "AND";
			$q = "
				SELECT
					objects.name as name,
					objects.oid as oid,
					kliendibaas_isik.birthday as bd
				FROM
					objects  LEFT JOIN kliendibaas_isik ON kliendibaas_isik.oid = objects.brother_of
				WHERE
					objects.class_id = '145' AND
					objects.status > 0  AND
					kliendibaas_isik.birthday != '' AND kliendibaas_isik.birthday != 0 AND kliendibaas_isik.birthday is not null $p_id_filt
			";
			$this->db_query($q);
			while ($row = $this->db_next())
			{
				list($y, $m, $d) = explode("-", $row["bd"]);
				if (($s_m > $e_m ? ($m >= $s_m || $m <= $e_m) : ($m >= $s_m && $m <= $e_m)))
				{
					$evts[$row["oid"]] = $row["oid"];
				}
			}
		}

		foreach($evts as $obj_id=>$obj)
		{
			if(!$this->can("view", $obj_id))
			{
				continue;
			}
			if(is_oid($obj))
			{
				$item = new object($obj_id);
			}
			else
			{
				$item = $obj;
			}
			// relative needs last n and next m items, those might be
			// outside of the current range
			$date = $item->prop("start1");
			if ($item->class_id() == CL_CRM_DOCUMENT_ACTION)
			{
				$date = $item->prop("date");
			}
			else
			if ($item->class_id() == CL_CRM_PERSON && $calo)
			{
				$ds = $calo->prop("day_start");
				$bd = $item->prop("birthday");
				list($y, $m, $d) = explode("-", $bd);
				$date = mktime($ds["hour"], $ds["minute"], 0, $m, $d, date("Y"));
			}
			else
			if($item->class_id() == CL_BUG)
			{
				$date  = $item->prop("deadline");
			}

			// if this thing has recurrences attached, then stick those in there
			$recurs = array();
			foreach($item->connections_from(array("type" => "RELTYPE_RECURRENCE")) as $c)
			{
				// get all times for this one from the recurrence table
				$this->db_query("SELECT recur_start, recur_end from recurrence where recur_id = ".$c->prop("to")." AND recur_start > $overview_start");
				while ($row = $this->db_next())
				{
					$recurs[] = $row;
				}
			}

			if ($range["viewtype"] != "relative" && ($date < $overview_start) && count($recurs) == 0)
			{
				continue;
			};

			$icon = icons::get_icon_url($item);

			if ($item->class_id() == CL_CRM_DOCUMENT_ACTION)
			{
				$t_c = reset($item->connections_to());
				$t_o = $t_c->from();
				$link = $this->mk_my_orb("change",array(
					"id" => $t_o->id(),
					"return_url" => $return_url,
				),$t_o->class_id());
			}
			else
			if ($item->class_id() == CL_DOCUMENT)
			{
				$link = $this->mk_my_orb("change",array(
					"id" => $item->id(),
					"return_url" => $return_url,
				),CL_DOCUMENT);
			}
			if ($item->class_id() == CL_CRM_PERSON)
			{
				$link = $this->mk_my_orb("change",array(
					"id" => $item->id(),
					"return_url" => $return_url,
				),CL_CRM_PERSON);
			}
			if($item->class_id() == CL_BUG)
			{
				$link = $this->mk_my_orb("change", array(
					"id" => $item->id(),
					"return_url" => $return_url,
				), CL_BUG);
			}
			else
			{
				$link = $planner->get_event_edit_link(array(
					"cal_id" => $this->cal_id,
					"event_id" => $item->id(),
					"return_url" => $return_url,
				));
			};

			// if this thing has recurrences attached, then stick those in there
			foreach($recurs as $row)
			{
				$rd = $row["recur_start"];
				if ($rd > ($start-(7*24*3600)))
				{
					$t->add_item(array(
						"timestamp" => $rd,
						"item_start" => $rd,
						"item_end" => $row["recur_end"],
						"data" => array(
							"name" => $item->class_id() == CL_CRM_PERSON ? sprintf(t("%s s&uuml;nnip&auml;ev!"), $item->name()) : $item->name(),
							"link" => $link,
							"modifiedby" => $item->prop("modifiedby"),
							"icon" => $icon,
							'comment' => $item->comment(),
						),
					));
				}

				if ($rd > $overview_start)
				{
					$this->overview[$rd] = 1;
				};
			}

			if ($date > ($start-(7*24*3600)))
			{

				$t->add_item(array(
					"timestamp" => $date,
					"item_start" => ($item->class_id() == CL_CRM_MEETING ? $item->prop("start1") : NULL),
					"item_end" => ($item->class_id() == CL_CRM_MEETING ? $item->prop("end") : NULL),
					"data" => array(
						"name" => $item->class_id() == CL_CRM_PERSON ? sprintf(t("%s s&uuml;nnip&auml;ev!"), $item->name()) : $item->name(),
						"link" => $link,
						"modifiedby" => $item->prop("modifiedby"),
						"icon" => $icon,
						'comment' => $item->comment(),
					),
				));
			}

			if ($date > $overview_start)
			{
				$this->overview[$date] = 1;
			}
		}
	}

	function _get_tasks_call($arr)
	{
		$prop = &$arr["prop"];
		$obj = $arr["obj_inst"];
		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_CALL",
		));
		$rv = "";
		foreach($conns as $conn)
		{
			$target_obj = $conn->to();
			$inst = $target_obj->instance();
			if (method_exists($inst,"request_execute"))
			{
				$rv .= $inst->request_execute($target_obj);
			}
		}
		$prop["value"] = $rv;
	}

	function _get_mail_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "subject",
			"caption" => t("Teema"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "to",
			"caption" => t("Kellele"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
		$t->set_caption("Kirjad");

		$cur = get_current_company();
		$parents[] = $cur->id();
		$mf = $cur->get_first_obj_by_reltype("RELTYPE_MAILS_FOLDER");
		if($mf)
		{
			$parents[] = $mf->id();
		}

		$filt = array(
			"parent" => $parents,
			"class_id" => CL_MESSAGE
		);

		if($arr["request"]["id"] != $cur->id())
		{
			$c = $arr["obj_inst"];
			$mails = $c->connections_from(array(
				"type" => "RELTYPE_EMAIL"
			));
			$emails = array();
			foreach($mails as $mail)
			{
				$emails[] = $mail->prop("to.name");
			}
			$filt["mto"] = $emails;
		}

		if($arr["request"]["mail_s_subj"])
		{
			$filt["name"] = '%'.$arr["request"]["mail_s_subj"].'%';
		}

		if($arr["request"]["mail_s_body"])
		{
			$filt["message"] = '%'.$arr["request"]["mail_s_body"].'%';
		}

		if($arr["request"]["mail_s_to"])
		{
			if($filt["mto"])
				$filt["mto"][] = '%'.$arr["request"]["mail_s_to"].'%';
			else
				$filt["mto"] = '%'.$arr["request"]["mail_s_to"].'%';
		}
		$ol = new object_list($filt);
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id(),
				"subject" => html::obj_change_url($o->id(),$o->name()),
				"to" => $o->prop("mto"),
			));
		}
	}

	function _get_mail_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_delete_button();
	}

	function _init_my_tasks_t($t, $data = false, $r = array() , $group = 0)
	{
		if (is_array($data) && $r["act_s_print_view"] != 1)
		{
			$filt = array();
			foreach($data as $row)
			{
				$filt["customer"][] = strip_tags($row["customer"]);
				$filt["proj_name"][] = strip_tags($row["proj_name"]);
				$filt["priority"][] = strip_tags($row["priority"]);
				$part = strip_tags($row["parts"]);
				foreach(explode(",", $part) as $nm)
				{
					$filt["parts"][] = trim($nm);
				}
			}
		}

		$t->define_field(array(
			"caption" => t("&nbsp;"),
			"name" => "icon",
			"align" => "center",
//			"chgbgcolor" => "col",
			"sortable" => 1,
			"width" => 1
		));

		if ($r["group"] == "meetings")
		{
			$t->define_field(array(
				"caption" => t("Toimumisaeg"),
				"name" => "when",
				"align" => "center",
	//			"chgbgcolor" => "col",
			));
		}
		if(!$group)
		{
			$t->define_field(array(
				"caption" => t("Klient"),
				"name" => "customer",
				"align" => "center",
	//			"chgbgcolor" => "col",
				"sortable" => 1,
				"filter" => array_unique($filt["customer"])
			));

			$t->define_field(array(
				"caption" => t("Projekt"),
				"name" => "proj_name",
				"align" => "center",
	//			"chgbgcolor" => "col",
				"sortable" => 1,
				"filter" => array_unique($filt["proj_name"])
			));
		}

		$t->define_field(array(
			"caption" => t("Pealkiri"),
			"name" => "name",
			"align" => "left",
//			"chgbgcolor" => "col",
			"sortable" => 1,
			"colspan" => "colspan",
		));


		if ($r["group"] != "meetings")
		{
			$t->define_field(array(
				"caption" => t("Aeg"),
				"name" => "deadline",
				"align" => "center",
				"sortable" => 1,
				"numeric" => 1,
				//"type" => "time",
				"chgbgcolor" => "col",
				//"format" => "d.m.Y H:i",
				"callback" => array($this, "_format_deadline"),
				"callb_pass_row" => 1
			));
		}

		if ($r["group"] != "ovrv_offers")
		{
			$t->define_field(array(
				"caption" => t("Prioriteet"),
				"name" => "priority",
	//			"chgbgcolor" => "col",
				"align" => "center",
				"sortable" => 1,
				"numeric" => 1,
				"filter" => array_unique($filt["priority"])
			));
		}

		$t->define_field(array(
			"caption" => t("Osalejad"),
			"name" => "parts",
			"align" => "center",
//			"chgbgcolor" => "col",
			"sortable" => 1,
			"filter" => array_unique($filt["parts"])
		));

		if ($r["act_s_print_view"] != 1)
		{
			$t->define_chooser(array(
		//			"chgbgcolor" => "col",
				"field" => "oid",
				"name" => "sel"
			));
			$t->define_field(array(
				"caption" => t("&nbsp;"),
				"name" => "menu",
				"align" => "center",
	//			"chgbgcolor" => "col",
			));
		}
	}

	function __task_sorter($a, $b)
	{//return $a->id() > $b->id() ? -2 : ($a->id() < $b->id() ? 2 : 0);

		$b_proj = $b->prop("project");
		$a_proj = $a->prop("project");
		$b_cust = $b->prop("customer");
		$a_cust = $a->prop("customer");

		if(($a_cust - $b_cust) == 0)
		{
			if(($a_proj - $b_proj) == 0)
			{
				$a_d = $a->prop("deadline");
				$b_d = $b->prop("deadline");
				if($a_d == $b_d)
				{
					return strcmp($a->name(), $b->name());
				}
				else
				{
					return ($a_d - $b_d);
				}
			}
			else
			{
				$b_project_name = ($b_project)?$b->prop("project.name"):"";
				$a_project_name = ($a_project)?$a->prop("project.name"):"";
				return strcmp($a_project_name, $b_project_name);
			}
		}
		else
		{
			$b_cust_name = ($b_cust)?$b->prop("customer.name"):"";
			$a_cust_name = ($a_cust)?$a->prop("customer.name"):"";
			return strcmp($a_cust_name, $b_cust_name);
		}
		return $a->prop("date") - $b->prop("date");
	}

	function _get_my_tasks($arr)
	{
		$seti = new crm_settings();
		$sts = $seti->get_current_settings();
		if(is_object($sts) && $sts->prop("group_task_view"))
		{
			$group = 1;
		}
		if (aw_global_get("crm_task_view") != CRM_TASK_VIEW_TABLE)
		{
			return PROP_IGNORE;
		}

		$ol = $this->_get_task_list($arr);
		$olarr = $ol->arr();
		$this->_preload_customer_list_for_tasks($olarr);
		if($arr["request"]["group"] !== "ovrv_mails")
		{
			$ol->sort_by_cb(array($this, "__task_sorter"));
		}

		if ($arr["request"]["group"] === "ovrv_offers")
		{
			return $this->_get_ovrv_offers($arr, $ol);
		}

		$pm = new popup_menu();
		// make task2person list
		$task2person = $this->_get_participant_list_for_tasks($ol->ids());
		$task2recur = $this->_get_recur_list_for_tasks($ol->ids());

		$task_nr = 0;
		$table_data = array();

		$last_cust = $last_proj = 0;
		$ti = new task();

		foreach($ol->ids() as $task_id)
		{
			$task_nr++;
			$task = obj($task_id);
			$cust = $task->prop("customer");
			$cust_name = "";

			$cust_str = "";
			if(!$this->can("view", $cust))
			{
				$cust_o = $task->get_first_obj_by_reltype(array("type" => "RELTYPE_CUSTOMER"));
				if(is_object($cust_o))
				{
					$cust = $cust_o->id();
				}
			}
			if (is_oid($cust) && $this->can("view", $cust))
			{
				$cust_o = obj($cust);
				$cust_str = html::get_change_url($cust, array("return_url" => get_ru()), parse_obj_name($cust_o->name()));
				$cust_name = $cust_o->name();
			}

			if($group)
			{
				if($last_proj != $task->prop("project"))
				{
					if($this->can("view" , $task->prop("project")))
					{
						$proj = obj($task->prop("project"));
						$table_data[] = array(
							"name" => "<h3>".$cust_name." - ". $proj->name()."</h3>",
							"colspan" => 4,
						);
					}
					$last_proj = $task->prop("project");
				}
			}

			if($group)
			{
/*				if($last_cust != $task->prop("customer"))
				{
					$table_data[] = array(
						"name" => "<h3>".$cust->name()."</h3>",
					);
					$last_cust = $task->prop("customer");
				}
*/
				if($last_proj != $task->prop("project"))
				{
					if($this->can("view" , $task->prop("project")))
					{
						$proj = obj($task->prop("project"));
						$table_data[] = array(
							"name" => "<h3>".$cust_name." - ". $proj->name()."</h3>",
							"colspan" => 4,
						);
					}
					$last_proj = $task->prop("project");
				}
			}



			$proj = $task->prop("project");
			$proj_str = "";
			if (is_oid($proj) && $this->can("view", $proj))
			{
				$proj_o = obj($proj);
				$proj_str = html::get_change_url($proj, array("return_url" => get_ru()), parse_obj_name($proj_o->name()));
			}

			$col = "";
			if ($task->class_id() == CL_CRM_EMAIL)
			{
				$dl = $task->prop("date");
			}
			else
			if ($task->class_id() == CL_CRM_MEETING || $task->class_id() == CL_CRM_CALL || $task->class_id() == CL_CRM_OFFER)
			{
				$dl = $task->prop("start1");
			}
			else
			{
				$dl = $task->prop("deadline");
			}

			if ($task->class_id() != CL_CRM_EMAIL)
			{
				$color = 0;
				if($task->class_id() == CL_BUG)
				{
					$s = $task->prop("bug_status");
					if($s != 5 && $s != 3)
					{
						$color = 1;
					}
				}
				elseif($task->prop("flags") != OBJ_IS_DONE)
				{
					$color = 1;
				}
				if($color)
				{
					if ($dl > 100 && time() > $dl)
					{
						$col = "#ff0000";
					}
					else
					if ($dl > 100 && date("d.m.Y") == date("d.m.Y", $dl)) // today
					{
						$col = "#f3f27e";
					}
				}
			}

			$ns = array();
			foreach(safe_array($task2person[$task->id()]) as $p_oid)
			{
				$ns[] = html::obj_change_url($p_oid);
			}

			$t_id = $task->id();
			$pm->begin_menu("task_".$t_id);
			$pm->add_item(array(
				"text" => t("Ava read"),
				"link" => $this->mk_my_orb("change", array(
					"id" => $t_id,
					"group" => "rows",
					"return_url" => get_ru()
				), $task->class_id())
			));

			$link = $this->mk_my_orb("mark_tasks_done", array(
					"sel" => array($t_id => $t_id),
					"post_ru" => "a"
				), CL_CRM_COMPANY);

			$done_ic_url = aw_ini_get("baseurl")."/automatweb/images/icons/class_".$task->class_id()."_done.gif";
			$pm->add_item(array(
				"text" => t("M&auml;rgi tehtuks"),
				"onclick" => "bg_mark_task_done(\"{$link}\", \"task{$task_nr}\", \"{$done_ic_url}\");",
				"link" => "javascript:void(0)"
			));
			$pm->add_item(array(
				"text" => t("Koosta arve"),
				"link" => $this->mk_my_orb("create_bill_from_task", array(
					"id" => $t_id,
					"post_ru" => get_ru()
				), CL_TASK)
			));
			if (!$ti->stopper_is_running($task->id()))
			{
				$url = $this->mk_my_orb("stopper_pop", array(
					"id" => $t_id,
					"s_action" => "start",
					"type" => t("Toimetus"),
					"name" => $task->name(),
				),CL_TASK);

				$pm->add_item(array(
					"text" => t("K&auml;ivita stopper"),
					"link" => "#",
					"onclick" => "aw_popup_scroll(\"{$url}\",\"aw_timers\",320,400)"
				));
			}
			else
			{

				$url = $this->mk_my_orb("stopper_pop", array(
					"id" => $t_id,
					"s_action" => "stop",
					"type" => t("Toimetus"),
					"name" => $task->name()
				),CL_TASK);

				$elapsed = $ti->get_stopper_time($task->id());
				$hrs = (int)($elapsed / 3600);
				$mins = (int)(($elapsed - ($hrs * 3600)) / 60);
				$elapsed = sprintf("%02d:%02d", $hrs, $mins);

				$pm->add_item(array(
					"text" => sprintf(t("Peata stopper (%s)"), $elapsed),
					"link" => "#",
					"onclick" => "aw_popup_scroll(\"{$url}\",\"aw_timers\",320,400)"
				));
			}
			$pm->add_item(array(
				"text" => t("Kustuta"),
				"onclick" => "if(!confirm(\"".t("Olete kindel et soovide toimetust kustutada?")."\")) { return false; } else {window.location = \"".$this->mk_my_orb("delete_tasks", array(
					"sel" => array($t_id => $t_id),
					"post_ru" => get_ru()
				), CL_CRM_COMPANY)."\"}",
				"link" => "#"
			));
			// if this thing has recurrences attached, then stick those in there
			$recurs = array();
			foreach(safe_array($task2recur[$task->id()]) as $recur_id)
			{
				$task_nr++;
				// get all times for this one from the recurrence table
				$this->db_query("SELECT recur_start, recur_end from recurrence where recur_id = ".$recur_id);
				while ($row = $this->db_next())
				{
					$table_data[] = array(
						"icon" => html::img(array(
							"url" => icons::get_icon_url($task),
							"id" => "task$task_nr"
						)),
						"customer" => $cust_str,
						"proj_name" => $proj_str,
						"name" => html::get_change_url($task->id(), array("return_url" => get_ru()), parse_obj_name($task->name())),
						"deadline" => $dl,
						"end" => $row["recur_end"],
						"start" => $row["recur_start"],
						"oid" => $task->id(),
						"priority" => $task->prop("priority"),
						"col" => $col,
						"parts" => join(", ", $ns),
						"menu" => $pm->get_menu(),
						"when" => date("d.m.Y H:i", $row["recur_start"])." - ".date("d.m.Y H:i",$row["recur_end"]),
						"is_recur" => 1
					);
				}
			}

			$namp = "";
			if ($task->class_id() == CL_TASK)
			{
				$url = $this->mk_my_orb("get_task_row_table", array("id" => $task_id, "company" => $arr["obj_inst"]->id()));
				$namp = " (<a id='tnr$task_nr' href='javascript:void(0)' onClick='
					if ((trel = document.getElementById(\"trows$task_nr\")))
					{
						if (trel.style.display == \"none\")
						{
							if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0)
							{
								trel.style.display= \"block\";
							}
							else
							{
								trel.style.display= \"table-row\";
							}
						}
						else
						{
							trel.style.display=\"none\";
						}
						return false;
					}
					el=document.getElementById(\"tnr$task_nr\");
					td = el.parentNode;
					tr = td.parentNode;

					tbl = tr;
					while(tbl.tagName.toLowerCase() != \"table\")
					{
						tbl = tbl.parentNode;
					}
					p_row = tbl.insertRow(tr.rowIndex+1);
					p_row.className=\"awmenuedittablerow\";
					p_row.id=\"trows$task_nr\";
					n_td = p_row.insertCell(-1);
					n_td.className=\"awmenuedittabletext\";
					n_td.innerHTML=\"&nbsp;\";
					n_td = p_row.insertCell(-1);
					n_td.className=\"awmenuedittabletext\";
					n_td.innerHTML=\"&nbsp;\";
					n_td = p_row.insertCell(-1);
					n_td.className=\"awmenuedittabletext\";
					n_td.innerHTML=aw_get_url_contents(\"$url\");
					n_td.colSpan=9;
				'>".t("Read")."</a>) ";
			}

			$table_data[] = array(
				"icon" => html::img(array(
					"url" => icons::get_icon_url($task),
					"id" => "task$task_nr"
				)),
				"customer" => $cust_str,
				"proj_name" => $proj_str,
				"name" => html::get_change_url($task->id(), array("return_url" => get_ru()), parse_obj_name($task->name())).$namp,
				"deadline" => $dl,
				"end" => $task->prop("end"),
				"oid" => $task->id(),
				"priority" => ($task->class_id()==CL_BUG)? $task->prop("bug_priority"): $task->prop("priority"),
				"col" => $col,
				"parts" => join(", ", $ns),
				"menu" => $pm->get_menu(),
				"when" => date("d.m.Y H:i", $task->prop("start1"))." - ".date("d.m.Y H:i",$task->prop("end"))
			);
		}

		$t = $arr["prop"]["vcl_inst"];
		$this->_init_my_tasks_t($t, $table_data, $arr["request"], $group);

		foreach($table_data as $row)
		{
			if ($row["deadline"] > 100 || ($_GET["sortby"] != "" && $_GET["sortby"] != "deadline"))
			{
				$t->define_data($row);
			}
		}
		$t->set_default_sortby("deadline");
		$t->set_default_sorder("asc");

		$t->sort_by(array(
			"field" => $arr["request"]["sortby"],
			"sorder" => ($arr["request"]["sortby"] == "priority" ? "desc" : $arr["request"]["sort_order"])
		));

		$t->set_sortable(false);
		if (!($_GET["sortby"] != "" && $_GET["sortby"] != "deadline"))
		{
			foreach($table_data as $row)
			{
				if ($row["deadline"] < 100)
				{
					$t->define_data($row);
				}
			}
		}

		if ($arr["request"]["act_s_print_view"] == 1)
		{
			$sf = new aw_template;
			$sf->db_init();
			$sf->tpl_init("automatweb");
			$sf->read_template("index.tpl");
			$sf->vars(array(
				"content"	=> $t->draw(),
				"uid" => aw_global_get("uid"),
				"charset" => aw_global_get("charset")
			));
			die($sf->parse());
		}
	}

	function _get_tasks_search_filt($r, $tasks, $clid)
	{
		if (!in_array(CL_BUG, $clid))//ilma annab errorit...suht iga filtri peale otsib bugist ka.
		{
			$clid[] = CL_BUG;
		}
		$res = array(
			"class_id" => $clid,
			"brother_of" => new obj_predicate_prop("id")
		);
		if (count($tasks))
		{
			$res["oid"] = $tasks;
		}

		$clss = aw_ini_get("classes");
		if (is_array($clid))
		{
			$def = "CL_TASK";
		}
		else
		{
			$def = $clss[$clid]["def"];
		}
		if ($r["act_s_cust"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_cust"]);
			if ($clid == CL_CRM_DOCUMENT_ACTION)
			{
				$res[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						$def.".document(CL_CRM_DEAL).customer.name" => $str_filt,
						$def.".document(CL_CRM_MEMO).customer.name" => $str_filt
					)
				));
			}
			elseif(is_array($clid))
			{
				$res[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_BUG.customer(CL_CRM_COMPANY).name" => $str_filt,
						$def.".RELTYPE_CUSTOMER.name" => $str_filt
					)
				));
			}
			else
			{
				$res[$def.".customer(CL_CRM_COMPANY).name"] = $str_filt;
			}
		}

		$r["act_s_dl_from"] = date_edit::get_timestamp($r["act_s_dl_from"]);
		$r["act_s_dl_to"] = date_edit::get_timestamp($r["act_s_dl_to"]);
		if ($r["act_s_cal_name"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_cal_name"]);
			$cal_list = new object_list(array(
				"class_id" => CL_PLANNER,
				"name" => $str_filt
			));
			$oids = array();
			$pm = new planner_model();
			foreach($cal_list->arr() as $cal)
			{
				$parms = array(
					"id" => $cal->id()
				);
				$parms["start"] = $r["act_s_dl_from"];

				if ($r["act_s_dl_to"] > 300)
				{
					$parms["end"] = $r["act_s_dl_to"];
				}
				else
				{
					$parms["end"] = time() + 10 * 365 * 24 * 3600;
				}
				$tmp = $pm->get_event_list($parms);
				foreach($tmp as $_id => $dat)
				{
					$oids[] = $_id;
				}
			}
			if (count($oids))
			{
				$res["oid"] = $oids;
			}
			else
			{
				$res["oid"] = -1;
			}
		}

		if ($r["act_s_part"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_part"]);
			if ($clid == CL_CRM_DOCUMENT_ACTION)
			{
				$res[$def.".actor.name"] = $str_filt;//map("%%%s%%", explode(",", $r["act_s_part"]));
			}
			else
			if ($clid == CL_CRM_OFFER)
			{
				$res["CL_CRM_OFFER.RELTYPE_SALESMAN.name"] = $str_filt; //map("%%%s%%", explode(",", $r["act_s_part"]));
			}
			else
			if($clid == CL_BUG)
			{
				$res["CL_BUG.RELTYPE_MONITOR.name"] = $str_filt;
			}
			else
			{
				// since someone stupidly decided that task participant relations are FROM person TO task, not the other way around (duh)
				// we need to select all tasks here and the pass the oids to the rest of the filter

				// get the person(s) typed
				$persons = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"name" => $str_filt //map("%%%s%%", explode(",", $r["act_s_part"])),
				));
				if (!$persons->count())
				{
					$_res["oid"] = -1;
				}
				else
				{
					$c = new connection();
					$conns = $c->find(array(
						"from" => $persons->ids(),
						"from.class_id" => CL_CRM_PERSON,
						"to.class_id" => $clid,
						//"type" => "RELTYPE_PERSON_TASK"
					));
					$oids = array();
					foreach($conns as $con)
					{
						if (!isset($res["oid"]) || !isset($res["oid"][$con["to"]]))
						{
							$oids[] = $con["to"];
						}
					}
					$conns2 = $c->find(array(
						"to" => $persons->ids(),
						"to.class_id" => CL_CRM_PERSON,
						"type" => "RELTYPE_MONITOR",
						"from.class_id" => CL_BUG,
					));
					foreach($conns2 as $con)
					{
						if (!isset($res["oid"]) || !isset($res["oid"][$con["from"]]))
						{
							$oids[] = $con["from"];
						}
					}
					if (count($oids))
					{
						$_res["oid"] = $oids;
					}
					else
					{
						$_res["oid"] = 1;
					}
				}

				// also search from connected resources
				if ($clid != CL_CRM_CALL)
				{
					$res[] = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"oid" => $_res["oid"],
							$def.".RELTYPE_RESOURCE.name" => $str_filt //map("%%%s%%", explode(",", $r["act_s_part"]))
						)
					));
				}
				else
				{
					$res["oid"] = $_res["oid"];
				}
			}
		}
		if ($r["act_s_task_name"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_task_name"]);
			if ($clid == CL_CRM_DOCUMENT_ACTION)
			{
				$res[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"name" =>  $str_filt, //"%".$r["act_s_task_name"]."%",
						"CL_CRM_DOCUMENT_ACTION.document.name" => $str_filt // "%".$r["act_s_task_name"]."%",
					)
				));
			}
			else
			{
				$res["name"] = $str_filt; //"%".$r["act_s_task_name"]."%";
			}
		}
		if ($r["act_s_task_content"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_task_content"]);
			if ($clid == CL_CRM_EMAIL)
			{
				$res["content"] = $str_filt;
			}
			else
			{
				$res[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"content" => $str_filt, //"%".$r["act_s_task_content"]."%",
						"CL_TASK.RELTYPE_ROW.content" => $str_filt, //"%".$r["act_s_task_content"]."%",
						"CL_CRM_MEETING.content" => $str_filt,
						"CL_BUG.bug_content" => $str_filt
					)
				));
			}
		}
		if ($r["act_s_mail_content"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_mail_content"]);
			$res["content"] = $str_filt;
		}

		if ($r["act_s_mail_name"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_mail_name"]);
			$res["name"] = $str_filt; //"%".$r["act_s_task_name"]."%";
		}
		if ($r["act_s_code"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_code"]);
			$res["code"] = $str_filt; //"%".$r["act_s_code"]."%";
		}
		if ($r["act_s_proj_name"] != "")
		{
			$str_filt = $this->_get_string_filt($r["act_s_proj_name"]);
			if ($clid == CL_CRM_DOCUMENT_ACTION)
			{
				$res[$def.".document.project.name"] = $str_filt; //"%".$r["act_s_proj_name"]."%";
			}
			elseif(is_array($clid))
			{
				$res[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_BUG.project(CL_PROJECT).name" => $str_filt,
						$def.".project(CL_PROJECT).name" => $str_filt
					)
				));
			}
			else
			{
				$res[$def.".project(CL_PROJECT).name"] = $str_filt;  //"%".$r["act_s_proj_name"]."%";
			}
		}

		$dl = "deadline";
		if ($clid == CL_CRM_OFFER || $clid == CL_CRM_MEETING || $clid == CL_CRM_CALL )
		{
			$dl = "start1";
		}
		else
		if ($clid == CL_CRM_DOCUMENT_ACTION)
		{
			$dl = "date";
		}

		if (is_array($clid))
		{
			$dls = array("deadline", "start1", "CL_BUG.deadline");
			$cond = array();
			foreach($dls as $dl)
			{
				if ($r["act_s_dl_from"] > 1 && $r["act_s_dl_to"] > 1)
				{
					$cond[$dl] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $r["act_s_dl_from"], $r["act_s_dl_to"]);
				}
				else
				if ($r["act_s_dl_from"] > 1)
				{
					$cond[$dl] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["act_s_dl_from"]);
				}
				else
				if ($r["act_s_dl_to"] > 1)
				{
					$cond[$dl] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $r["act_s_dl_to"]);
				}
			}
			$res[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $cond
			));
		}
		else
		{
			if ($r["act_s_dl_from"] > 1 && $r["act_s_dl_to"] > 1)
			{
				$res[$dl] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $r["act_s_dl_from"], $r["act_s_dl_to"]);
			}
			else
			if ($r["act_s_dl_from"] > 1)
			{
				$res[$dl] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["act_s_dl_from"]);
			}
			else
			if ($r["act_s_dl_to"] > 1)
			{
				$res[$dl] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $r["act_s_dl_to"]);
			}
		}
		if ($r["act_s_status"] > 0 && $r["act_s_status"] < 3)
		{
			if ($clid == CL_CRM_DOCUMENT_ACTION)
			{
				$res["is_done"] = $r["act_s_status"] == 1 ? 0 : 1;
			}
			else
			{
				$res["flags"] = array("mask" => OBJ_IS_DONE, "flags" => $r["act_s_status"] == 1 ? 0 : OBJ_IS_DONE);
			}
		}
		$res[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
				"class_id" => new obj_predicate_not(CL_BUG),
				"CL_BUG.bug_status" => array(1,2,10,11),
//				"CL_TASK.oid" => new obj_predicate_compare(OBJ_COMP_GREATER, 0)
			)
		));
		return $res;
	}

	function _get_my_tasks_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$pl = new planner();
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$clids = array(CL_TASK => 13, CL_CRM_MEETING => 11, CL_CRM_CALL => 12, CL_CRM_OFFER => 9);
		$clss = aw_ini_get("classes");

		$u = get_instance(CL_USER);
		$cur_co = $u->get_current_company();

		foreach($clids as $clid => $relt)
		{
			$url = $this->mk_my_orb('new',array(
				'alias_to_org' => $cur_co == $arr['obj_inst']->id() ? null : $arr['obj_inst']->id(),
				'reltype_org' => $relt,
				'add_to_cal' => $this->cal_id,
				'clid' => $clid,
				'title' => $clss[$clid]["name"],
				'parent' => $arr["obj_inst"]->id(),
				'return_url' => get_ru()
			), $clid);

			$tb->add_menu_item(array(
				'parent'=>'add_item',
				'text' => $clss[$clid]["name"],
				'link' => $url
			));
		}

		/*$tb->add_menu_item(array(
			'parent' => 'add_item',
			"text" => t("P&auml;eva raport"),
			'link' => html::get_new_url(
				CL_CRM_DAY_REPORT,
				$arr["obj_inst"]->id(),
				array(
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 39,
					"return_url" => get_ru()
				)
			),
		));*/

		$tb->add_button(array(
			'name' => 'mark_as_done',
			'img' => 'save.gif',
			'tooltip' => t('M&auml;rgi tehtuks'),
			'action' => 'mark_tasks_done',
		));

		$tb->add_button(array(
			'name' => 'delete_tasks',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta toimetused'),
			"confirm" => t("Oled kindel et soovid valitud toimetusi kustutada?"),
			'action' => 'delete_tasks',
		));

		$tb->add_separator();

		if (aw_global_get("crm_task_view") == CRM_TASK_VIEW_TABLE)
		{
			$tb->add_button(array(
				'name' => 'tasks_switch_to_cal',
				'img' => 'icon_cal_today.gif',
				'tooltip' => t('Kalendrivaade'),
				'action' => 'tasks_switch_to_cal_view',
			));
		}
		else
		{
			$tb->add_button(array(
				'name' => 'tasks_switch_to_table',
				'img' => 'class_'.CL_TABLE.'.gif',
				'tooltip' => t('Tabelivaade'),
				'action' => 'tasks_switch_to_table_view',
			));
		}
		if($arr["request"]["group"] == "ovrv_mails")
		{
			$mail_mgr = new crm_email_mgr();
			$tb->add_button(array(
				"name" => "user_calendar",
				"tooltip" => t("Impordi mailid"),
				"url" => $mail_mgr->mk_my_orb('upd_mails', array()),
				"onClick" => "",
				"img" => "mail_reply.gif",
			));

			$tb->add_menu_button(array(
				'name'=>'set_project',
				'tooltip'=> t('M&auml;&auml;ra projekt')
			));

			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_ORDERER.id" => $arr["obj_inst"]->id()
			));
			$u = get_instance(CL_USER);
			$cur_co = $u->get_current_company();
			foreach($ol->arr() as $o)
			{
				$url = $this->mk_my_orb("set_project_to_mail", array('proj' => $o->id()));

				$tb->add_menu_item(array(
					'parent'=>'set_project',
					'text' => $o->name(),
					"url" => "#",
					"onClick" => "document.changeform.proj.value='".$o->id()."';
						document.changeform.action.value='set_project_to_mail';
						document.changeform.submit()"
				));
			}
		}
	}

	function _get_act_s_part($arr)
	{
		if ($arr["request"]["act_s_sbt"] == "" && $arr["request"]["act_s_is_is"] != 1)
		{
			$u = get_instance(CL_USER);
			$p = obj($u->get_current_person());
			//$v = $p->name();
			switch($_GET["group"])
			{
				case "all_actions":
				case "my_tasks":
					if($p->has_tasks())
					{
						$v = $p->name();
					}
					break;
				case "meetings":
					if($p->has_meetings())
					{
						$v = $p->name();
					}
					break;
				case "calls":
					if($p->has_calls())
					{
						$v = $p->name();
					}
					break;
				case "ovrv_offers":
					if($p->has_ovrv_offers())
					{
						$v = $p->name();
					}
					break;
			}


// 			$col = new object_list(array(
// 				"class_id" => array(CL_CRM_MEETING, CL_CRM_CALL, CL_TASK, CL_BUG , CL_DOCUMENT),
// 				"name" => "%".$p->name()."%",
// 			));
// 			if($col->count())
// 			{
// 				$v = $p->name();
// 			}

		}
		else
		{
			$v = $arr["request"]["act_s_part"];
		}
		$tt = t("Kustuta");
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "act_s_part",
			"value" => $v,
			"size" => 25
		))."<a href='javascript:void(0)' title=\"$tt\" alt=\"$tt\" onClick='document.changeform.act_s_part.value=\"\"'><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
		return PROP_OK;
	}

	function _get_act_s_cal_name($arr)
	{
		if ($arr["request"]["act_s_sbt"] == "" && $arr["request"]["act_s_is_is"] != 1)
		{
			$cal = new planner();
			$p = $cal->get_calendar_for_user();
			if ($p)
			{
				$p = obj($p);
				$v = $p->name();
			}
		}
		else
		{
			$v = $arr["request"]["act_s_cal_name"];
		}
		$tt = t("Kustuta");
		$arr["prop"]["value"] = html::textbox(array(
			"name" => "act_s_cal_name",
			"value" => $v,
			"size" => 15
		))."<a href='javascript:void(0)' title=\"$tt\" alt=\"$tt\" onClick='document.changeform.act_s_cal_name.value=\"\"'><img title=\"$tt\" alt=\"$tt\" src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0></a>";
		return PROP_OK;
	}

	function _get_my_tasks_cal($arr)
	{
		if (aw_global_get("crm_task_view") != CRM_TASK_VIEW_CAL)
		{
			return PROP_IGNORE;
		}
		unset($arr["request"]["act_s_cust"]);
		unset($arr["request"]["act_s_status"]);
		unset($arr["request"]["act_s_print_view"]);
		unset($arr["request"]["act_s_sbt"]);
		unset($arr["request"]["act_s_is_is"]);
		unset($arr["request"]["act_s_mail_content"]);
		unset($arr["request"]["act_s_mail_name"]);
		$args = array();
		switch($arr["request"]["group"])
		{
			case "my_tasks":
			case "overview":
				$args["type"] = "RELTYPE_TASK";
				$clid = CL_TASK;
				break;

			case "meetings":
				$args["type"] = "RELTYPE_KOHTUMINE";
				$clid = CL_CRM_MEETING;
				break;

			case "calls":
				$args["type"] = "RELTYPE_CALL";
				$clid = CL_CRM_CALL;
				break;

			case "ovrv_offers":
				//$args["type"] = "RELTYPE_OFFER";
				$clid = CL_CRM_DOCUMENT_ACTION;
				break;

			default:
				$args["type"] = array("RELTYPE_TASK", "RELTYPE_KOHUTMINE", "RELTYPE_CALL", "RELTYPE_OFFER");
				$clid = array(CL_TASK, CL_CRM_MEETING, CL_CRM_CALL, CL_CRM_OFFER);
				break;
		}
		$this->do_org_actions($arr, $args, $clid);
	}

	function tree_tasks($arr)
	{
		$filter = array();
		$done = null;
		$undone = $over_deadline = 0;
		$ol = new object_list();
		$params = explode("_" , $arr["request"]["st"]);
		$time_params = explode("_" , $arr["request"]["tm"]);
		$type_params = explode("_" , $arr["request"]["tf"]);
		$stats = new crm_company_stats_impl();

		switch($params[0])
		{
			case "custman":
				$filter["client_manager"] = $params[1];
			case "prman":
				$filter["project_manager"] = $params[1];
				break;
			case "cust":
				$filter["customer"] = $params[1];
				break;
			case "my":
			default :
				$person = get_current_person();
				$filter["person"] = $person->id();
				break;
		}

		if(!$type_params[0])
		{
			$type_params[1] = "undone";
		}

		switch($type_params[0])
		{
/*			case "next":
				$start = time();
				$end = time() + DAY*1000;
				break;*/
/*			case "last_last_mon":
				$start = mktime(0,0,0,(date("m") - 2) , 1 , date("Y"));
				$end = mktime(0,0,0,(date("m") - 1) , 1 , date("Y"))-1;
				break;
			case "cur_year":
				$start = get_year_start();
				$end = mktime(0,0,0,1,1,(date("Y") + 1))-1;
				break;
			case "last_year":
				$start = mktime(0,0,0,1,1,(date("Y") - 1));
				$end = get_year_start()-1;
				break;*/
			case CL_BUG:
				$class_id = CL_BUG;
				if($type_params[1] > 0)
				{
					$filter["status"] = $type_params[1];
				}
				break;
			case CL_CRM_CALL:
				$class_id = CL_CRM_CALL;
				break;
			case CL_TASK:
				$class_id = CL_TASK;
				break;
			case CL_CRM_MEETING:
				$class_id = CL_CRM_MEETING;
				break;
			default:
				break;
		}

		switch($time_params[0])
		{
			case "all":
				$start = 1;
				$end = time()*2;
				break;
			case "currentmonth":
				$start = date_calc::get_month_start();
				$end = mktime(0,0,0,(date("m") + 1) , 1 , date("Y"))-1;
				break;
			case "lastmonth":
				$start = mktime(0,0,0,(date("m") - 1) ,1 , date("Y"));
				$end = date_calc::get_month_start()-1;
				break;
			case "currentweek":
			default:
				$start = date_calc::get_week_start();
				$end = date_calc::get_week_start()+7*DAY-1;
				break;
		}

		switch($type_params[1])
		{
				case "planned":
				case "undone":
					$filter["done"] = 0;
					break;
				case "done":
				case "past":
					$filter["done"] = 1;
					break;
				case "overdeadline":
					$filter["deadline"] = 1;
					break;
				default:
					break;
		}

		if(!$start)
		{
			$start = date_calc::get_week_start();
			$end = date_calc::get_week_start()+7*DAY-1;
		}

		$filter["between"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING,$start, $end);
		$filter["from"] = $start;
		$filter["to"] = $end;

		if(!$class_id || $class_id == CL_BUG)
		{
			$ol->add($stats->get_bugs($filter));
		}
		if(!$class_id || $class_id == CL_CRM_CALL)
		{
			$ol->add($stats->get_calls($filter));
		}
		if(!$class_id || $class_id == CL_CRM_MEETING)
		{
			$ol->add($stats->get_meetings($filter));
		}
		if(!$class_id || $class_id == CL_TASK)
		{
			$ol->add($stats->get_tasks($filter));
		}
		return $ol;
	}


	function _get_task_list($arr)
	{
		$search_vars = array("act_s_cust", "act_s_part" , "act_s_cal_name" , "act_s_task_name" , "act_s_task_content" , "act_s_code" , "act_s_proj_name" , "act_s_dl_from" , "act_s_dl_to" , "act_s_status" , "act_s_is_is", "act_s_mail_content" , "act_s_mail_name");

		$tree = 1;
		foreach($search_vars as $search_var)
		{
			if(!empty($arr["request"][$search_var]))
			{
				$tree = 0;
				break;
			}

		}
		if($tree)
		{
			return $this->tree_tasks($arr);
		}

		$u = get_instance(CL_USER);
		$co = $u->get_current_company();

		$i = new crm_company();
		$clid = NULL;
		switch($arr["request"]["group"])
		{
			case "my_tasks":
				$clid = array(CL_TASK);
				if ($co == $arr["obj_inst"]->id())
				{
					$tasks = $i->get_my_tasks(!($arr["request"]["act_s_sbt"] != "" || $arr["request"]["act_s_is_is"] == 1), $arr);

				}
				else
				{
					$ol = new object_list($arr["obj_inst"]->connections_from(array(
						"type" => "RELTYPE_TASK"
					)));

					$ol2 = new object_list(array(
						"class_id" => $clid,
						"customer" => $arr["obj_inst"]->id()
					));
					$ol->add($ol2);
					$tasks = $this->make_keys($ol->ids());
					if (!count($tasks))
					{
						$tasks = array(-1);
					}
				}
				break;

			case "bugs":
				$clid = CL_BUG;
				if ($co == $arr["obj_inst"]->id())
				{
					$tasks = $i->get_my_bugs($arr);

				}
				else
				{
					$ol = new object_list($arr["obj_inst"]->connections_to(array(
						"type" => "RELTYPE_CUSTOMER",
						"from.class_id" => CL_BUG,
					)));
					$tasks = $this->make_keys($ol->ids());
					if (!count($tasks))
					{
						$tasks = array(-1);
					}
				}
				break;
			case "meetings":

				$clid = CL_CRM_MEETING;
				if ($co == $arr["obj_inst"]->id())
				{
					$tasksi = $i->get_my_meetings($arr);
					$tasks = array();
					foreach($tasksi as $t_id)
					{
						$o = obj($t_id);
						if (!($o->flags() & OBJ_IS_DONE))
						{
							$tasks[$o->id()] = $o->id();
						}
					}
				}
				else
				{
					$ol = new object_list($arr["obj_inst"]->connections_from(array(
						"type" => "RELTYPE_KOHTUMINE"
					)));

					$ol2 = new object_list(array(
						"class_id" => $clid,
						"customer" => $arr["obj_inst"]->id()
					));
					$ol->add($ol2);
					$tasks = $this->make_keys($ol->ids());
					if (!count($tasks))
					{
						$tasks = array(-1);
					}
				}
				break;

			case "calls":

				$clid = CL_CRM_CALL;
				if ($co == $arr["obj_inst"]->id())
				{
					$tasksi = $i->get_my_calls($arr);
					$tasks = array();
					foreach($tasksi as $t_id)
					{
						$o = obj($t_id);
						if (!($o->flags() & OBJ_IS_DONE))
						{
							$tasks[$o->id()] = $o->id();
						}
					}
				}
				else
				{
					$ol = new object_list($arr["obj_inst"]->connections_from(array(
						"type" => "CL_CRM_CALL"
					)));

					$ol2 = new object_list(array(
						"class_id" => $clid,
						"customer" => $arr["obj_inst"]->id()
					));
					$ol->add($ol2);
					$tasks = $this->make_keys($ol->ids());
					if (!count($tasks))
					{
						$tasks = array(-1);
					}
				}
				break;

			case "ovrv_mails":
				if ($co == $arr["obj_inst"]->id())
				{
					// my company. show my emails?
				}
				else
				{
					$ol = new object_list(array(
						"class_id" => CL_CRM_EMAIL,
						"customer" => $arr["obj_inst"]->id()
					));
					$tasks = $this->make_keys($ol->ids());
				}
				$clid = CL_CRM_EMAIL;
				break;

			case "ovrv_offers":
				if ($co == $arr["obj_inst"]->id())
				{
					/// this tab got turned into docmanagement. whoo
					$clid = CL_CRM_DOCUMENT_ACTION;
					// now, find all thingies that I am part of
					$filt = array(
						"class_id" => CL_CRM_DOCUMENT_ACTION,
						"site_id" => array(),
						"lang_id" => array(),
						"actor" => $u->get_current_person(),
					);
				//	if (!($arr["request"]["act_s_sbt"] != "" || $arr["request"]["act_s_is_is"] == 1))
				//	{
				//		$filt["is_done"] = new obj_predicate_not(1);
				//	}
				}
				else
				{
					$clid = CL_CRM_DOCUMENT_ACTION;

					$offers = new object_list(array(
						"class_id" => CL_CRM_OFFER,
						"orderer" => $arr["obj_inst"]->id()
					));

					$filt = array(
						"class_id" => CL_CRM_DOCUMENT_ACTION,
						"CL_CRM_DOCUMENT_ACTION.RELTYPE_DOC" => $offers->ids()
					);
				}
				$ol = new object_list($filt);
				$tasks = $this->make_keys($ol->ids());
				break;

			default:
				$clid = array(CL_TASK,CL_CRM_MEETING,CL_CRM_CALL,CL_CRM_OFFER);
				$clid2 = array("CL_TASK","CL_CRM_MEETING","CL_CRM_CALL");
				$cali = new planner();
				$calid = $cali->get_calendar_for_user();
				if($calid)
				{
					$cal = obj($calid);
					$eec = $cal->prop("event_entry_classes");
					if($eec[CL_BUG])
					{
						$clid[] = CL_BUG;
					}
				}
				if ($co == $arr["obj_inst"]->id())
				{
					$tasks = array();
					$tg = $i->get_my_actions($arr);
					if (!count($tg))
					{
						$tasks = array();
					}
					else
					{
						$ol = new object_list(array(
							"oid" => $tg,
							"flags" => array("mask" => OBJ_IS_DONE, "flags" => 0)
						));
						$tasks = $this->make_keys($ol->ids());
					}
				}
				else
				{
					$ol = new object_list($arr["obj_inst"]->connections_from(array(
						"type" => array("RELTYPE_KOHTUMINE", "RELTYPE_CALL", "RELTYPE_TASK", "RELTYPE_DEAL", "RELTYPE_OFFER")
					)));
					$filtor = array();
					foreach($clid2 as $cl)
					{
						$filtor[$cl.".RELTYPE_CUSTOMER"] = $arr["obj_inst"]->id();
					}
					$filtor["CL_CRM_OFFER.RELTYPE_ORDERER"] = $arr["obj_inst"]->id();

					$ol2 = new object_list(array(
						0 => new object_list_filter(array(
							"logic" => "OR",
							"conditions" => $filtor,
						)),
						"class_id" => $clid,
//						"customer" => $arr["obj_inst"]->id(),
					));
					$ol->add($ol2);
					$tasks = $this->make_keys($ol->ids());
					if (!count($tasks))
					{
						$tasks = array(-1);
					}
				}
				break;
		}

		if ($arr["request"]["act_s_sbt"] != "" || $arr["request"]["act_s_is_is"] == 1)
		{
			// filter
			$param = $tasks;
			if ($co == $arr["obj_inst"]->id())
			{
				$param = array();
			}
			$p = $this->_get_tasks_search_filt($arr["request"], $param, $clid);
			$p["brother_of"] = new obj_predicate_prop("id");
			$ol = new object_list($p);
			return $ol;
		}
		else
		{
			if (!count($tasks))
			{
				$ol = new object_list();
				return $ol;
			}
			else
			{
				$ol = new object_list(array(
					"oid" => $tasks,
					"brother_of" => new obj_predicate_prop("id")
				));
				return $ol;
			}
		}

		if ($ol->count())
		{
			$ol = new object_list(array(
				"oid" => $ol->ids(),
				"brother_of" => new obj_predicate_prop("id")
			));
		}

		if ($arr["request"]["group"] == "bills_search")
		{
			// filter out all tasks that can not get bills
			$res = new object_list();
			$ti = new task();
			foreach($ol->arr() as $o)
			{
				$has = false;
				foreach($ti->get_task_bill_rows($o) as $row)
				{
					if ($row["on_bill"] == 1 && $row["amt"] > 0 && !is_oid($row["on_bill"]))
					{
						$has = true;
					}
				}
				if ($has)
				{
					$res->add($o);
				}
			}
			$ol = $res;
		}
		return $ol;
	}

	function _format_deadline($arg)
	{
		$o = obj($arg["oid"]);
		if ($arg["is_recur"] == 1)
		{
			if ($arg["start"] == $arg["end"])
			{
				return date("d.m.Y H:i", $arg["start"]);
			}
			return date("d.m.Y H:i", $arg["start"])." - ".date("d.m.Y H:i", $arg["end"]);
		}
		if ($o->class_id() == CL_TASK)
		{
			if ($arg["deadline"] > 1000)
			{
				$arg["deadline"] = date("d.m.Y H:i", $arg["deadline"]);
			}
			else
			{
				return "";
			}
		}
		else
		if ($arg["end"] > 1000 && $arg["end"] > $arg["deadline"] && $arg["end"] != $arg["deadline"])
		{
			$d1 = date("d.m.Y", $arg["deadline"]);
			$d2 = date("d.m.Y", $arg["end"]);
			if ($d1 == $d2)
			{
				$arg["deadline"] = $d1."<br>".date("H:i", $arg["deadline"])." - ".date("H:i", $arg["end"]);
			}
			else
			{
				$arg["deadline"] = date("d.m.Y H:i", $arg["deadline"])." - ".date("d.m.Y H:i", $arg["end"]);
			}
		}
		else
		if ($arg["deadline"] > 1000)
		{
			$arg["deadline"] = date("d.m.Y H:i", $arg["deadline"]);
		}
		else
		{
			return "";
		}

		return $arg["deadline"];
	}

	function _get_ovrv_offers($arr, $ol)
	{
		$pm = new popup_menu();
		$table_data = array();
		foreach($ol->ids() as $act_id)
		{
			$act = obj($act_id);
			$task_c = reset($act->connections_to());
			$task = $task_c->from();

			// if this has a predicate thingie, then check if that is done before showing it here
			$preds = safe_array($act->prop("predicate"));
			foreach($preds as $pred)
			{
				if ($this->can("view", $pred))
				{
					$pred = obj($pred);
					if ($pred->prop("is_done") != 1)
					{
						continue;
					}
				}
			}

			if ($task->class_id() == CL_CRM_OFFER)
			{
				$cust = $task->prop("orderer");
			}
			else
			{
				$cust = $task->prop("customer");
			}
			$cust_str = "";
			if (is_oid($cust) && $this->can("view", $cust))
			{
				$cust_o = obj($cust);
				$cust_str = html::get_change_url($cust, array("return_url" => get_ru()), parse_obj_name($cust_o->name()));
			}

			$proj = $task->prop("project");
			$proj_str = "";
			if (is_oid($proj) && $this->can("view", $proj))
			{
				$proj_o = obj($proj);
				$proj_str = html::get_change_url($proj, array("return_url" => get_ru()), parse_obj_name($proj_o->name()));
			}

			$col = "";
			$dl = $act->prop("date");
			if ($dl > 100 && time() > $dl)
			{
				$col = "#ff0000";
			}
			else
			if ($dl > 100 && date("d.m.Y") == date("d.m.Y", $dl)) // today
			{
				$col = "#f3f27e";
			}

			$ns = html::obj_change_url($act->prop("actor"));
			if ($ns != "")
			{
				$nso = obj($act->prop("actor"));
				$work = html::obj_change_url($nso->company_id());
				$ns .= ($work != "" ? ", ".$work : "");
			}

			$t_id = $task->id();

			$table_data[] = array(
				"icon" => html::img(array("url" => icons::get_icon_url($task))),
				"customer" => $cust_str,
				"proj_name" => $proj_str,
				"name" => html::get_change_url($task->id(), array("return_url" => get_ru()), parse_obj_name($task->name()))." / ".html::get_change_url($act->id(), array("return_url" => get_ru()), parse_obj_name($act->name())),
				"deadline" => $dl,
				"oid" => $act->id(),
				"col" => $col,
				"parts" => $ns,
			);
		}

		$t = $arr["prop"]["vcl_inst"];
		$this->_init_my_tasks_t($t, $table_data, $arr["request"]);

		$format = t('%s dokumendid');
		if ( isset($arr['request']['act_s_part']) )
		{
			$participants_name = $arr['request']['act_s_part'];
			if (!empty($participants_name))
			{
				$format .= t(', milles on %s osaline');
			}
		}
		else
		{
			$user_inst = get_instance(CL_USER);
			$user_obj = new object($user_inst->get_current_person());
			$participants_name = $user_obj->name();
			$format = t('%s dokumendid, milles on %s osaline');
		}
		$t->set_caption(sprintf($format, $arr['obj_inst']->name(), $participants_name));


		foreach($table_data as $row)
		{
			if ($row["deadline"] > 100 || ($_GET["sortby"] != "" && $_GET["sortby"] != "deadline"))
			{
				$t->define_data($row);
			}
		}
		$t->set_default_sortby("deadline");
		$t->set_default_sorder("asc");

		$t->sort_by(array(
			"field" => $arr["request"]["sortby"],
			"sorder" => ($arr["request"]["sortby"] == "priority" ? "desc" : $arr["request"]["sort_order"])
		));

		$t->set_sortable(false);
		if (!($_GET["sortby"] != "" && $_GET["sortby"] != "deadline"))
		{
			foreach($table_data as $row)
			{
				if ($row["deadline"] < 100)
				{
					$t->define_data($row);
				}
			}
		}

		if ($arr["request"]["act_s_print_view"] == 1)
		{
			$sf = new aw_template;
			$sf->db_init();
			$sf->tpl_init("automatweb");
			$sf->read_template("index.tpl");
			$sf->vars(array(
				"content"	=> $t->draw(),
				"uid" => aw_global_get("uid"),
				"charset" => aw_global_get("charset")
			));
			die($sf->parse());
		}
	}

	function _get_string_filt($s)
	{
		$this->dequote($s);
		// separated by commas delimited by "
		$p = array();
		$len = strlen($s);
		for ($i = 0; $i < $len; $i++)
		{
			if ($s[$i] == "\"" && $in_q)
			{
				// end of quoted string
				$p[] = $cur_str;
				$in_q = false;
			}
			else
			if ($s[$i] == "\"" && !$in_q)
			{
				$cur_str = "";
				$in_q = true;
			}
			else
			if ($s[$i] == "," && !$in_q)
			{
				$p[] = $cur_str;
				$cur_str = "";
			}
			else
			{
				$cur_str .= $s[$i];
			}
		}
		$p[] = $cur_str;
		$p = array_unique($p);

		return map("%%%s%%", $p);
	}

	function _get_participant_list_for_tasks($tasks)
	{
		if (count($tasks) == 0)
		{
			return array();
		}
		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_CRM_PERSON,
			"to" => $tasks
		));
		$ret = array();
		$plist = array();
		foreach($conns as $conn)
		{
			$ret[$conn["to"]][$conn["from"]] = $conn["from"];
			$plist[$conn["from"]] = $conn["from"];
		}
		// warm cache
		if (count($plist))
		{
			$ol = new object_list(array("oid" => $plist));
			$ol->arr();
		}
		$conns = $c->find(array(
			"from.class_id" => CL_BUG,
			"from" => $tasks,
			"type" => "RELTYPE_MONITOR",
			"to.class_id" => CL_CRM_PERSON
		));
		foreach($conns as $conn)
		{
			$ret[$conn["from"]][$conn["to"]] = $conn["to"];
		}
		return $ret;
	}

	function _get_recur_list_for_tasks($tasks)
	{
		$c = new connection();
		$conns = $c->find(array(
			"from" => $tasks,
			"to.class_id" => CL_RECURRENCE
		));
		$ret = array();
		foreach($conns as $conn)
		{
			$ret[$conn["from"]][$conn["to"]] = $conn["to"];
		}
		return $ret;
	}

	function _init_task_row_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Teostaja"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "when",
			"caption" => t("Millal"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center"
		));
		/*$t->define_field(array(
			"name" => "is_done",
			"caption" => t("Tehtud?"),
			"align" => "center"
		));*/
	}

	/**
		@attrib name=get_task_row_table
		@param id required
	**/
	function get_task_row_table($arr)
	{
		$sum = 0;
		global $company;
		$t = new vcl_table();
		$this->_init_task_row_table($t);
		$task = obj($arr["id"]);
		$company = obj($company);
		foreach($task->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$row = $c->to();
			if ($row->prop("done") == 1 && !$company->prop("all_action_rows"))
			{
				continue;
			}
			$url = $this->mk_my_orb("stopper_pop", array(
				"id" => $row->id(),
				"s_action" => "start",
				"type" => t("Toimetuse rida"),
				"name" => $row->prop("content"),
			), CL_TASK);
			$stopper = " <a href='#' onClick='aw_popup_scroll(\"$url\",\"aw_timers\",320,400)'>".t("Stopper")."</a>";
//if(aw_global_get("uid") == "Teddi.Rull") arr($row->id());
			$t->define_data(array(
				"name" => $row->prop("content"),
				"who" => html::obj_change_url($row->prop("impl")),
				"when" => date("d.m.Y",$row->prop("date") > 100 ? $row->prop("date") : $row->created()),
				"time" => $row->prop("time_real")."<br>".$stopper,
				"is_done" => $row->prop("done") ? t("Jah") : t("Ei")
			));
			$sum += $row->prop("time_real");
		}
		$t->define_data(array(
				"name" => t("Kokku:"),
				"time" => $sum,
		));

		die(iconv(aw_global_get("charset"), "utf-8", $t->draw()));
	}

	function _preload_customer_list_for_tasks($tasks)
	{
		$custs = array();
		foreach($tasks as $task_o)
		{
			if($task_o->prop("customer"))
			{
				$custs[$task_o->prop("customer")] = 1;
			}
		}
		$projs = array();
		foreach($tasks as $task_o)
		{
			if($task_o->prop("project"))
			{
				$projs[$task_o->prop("project")] = 1;
			}
		}

		if (count($custs))
		{
			$ol = new object_list(array(
				"oid" => array_keys($custs)
			));
			$ol->arr();
		}
		if (count($projs))
		{
			$ol = new object_list(array(
				"oid" => array_keys($projs)
			));
			$ol->arr();
		}
	}

// 	private function _add_leafs_to_tasks_tree($tree, $parent)
// 	{
// 		$types = array(
// 			CL_BUG => t("&Uuml;lesanne"),
// 			CL_TASK => t("Toimetus"),
// 			CL_CRM_MEETING => t("Kohtumine"),
// 			CL_CRM_CALL => t("K&otilde;ne")
// 		);
//
// 		$time_types = array(
// 			"currentweek" => t("Jooksev n&auml;dal"),
// 			"currentmonth" => t("Jooksev kuu"),
// 			"lastmonth" => t("Eelmine kuu"),
// 		);
//
//  		$call_params = array(
//  			"done" => t("Tehtud"),
//  			"planned" => t("Plaanis"),
//  		);
//
//  		$meeting_params = array(
//  			"past" => t("Toimunud"),
//  			"planned" => t("Tulekul"),
//  		);
//
//  		$task_params = array(
//  			"undone" => t("Tegemata"),
//  			"overdeadline" => t("&Uuml;le t&auml;htaja"),
//  		);
//
//  		$bugs_params = array(
//  			"undone" => t("Tegemata"),
//  			"overdeadline" => t("&Uuml;le t&auml;htaja"),
//  		);
//
// 		foreach($types as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent,array(
// 				"name" => $type,
// 				"id" => $parent."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".$type_id),
// 				"iconurl" => icons::get_icon_url($type_id),
// 			));
// 		}
//
// 		foreach($time_types as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent,array(
// 				"name" => $type,
// 				"id" => $parent."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".$type_id),
// 			));
// 		}
//
// 		foreach($call_params as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_CRM_CALL."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_CRM_CALL,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_CRM_CALL."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_CRM_CALL."_".$type_id),
// 			));
// 		}
// 		foreach($time_types as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_CRM_CALL."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_CRM_CALL,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_CRM_CALL."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_CRM_CALL."_".$type_id),
// 			));
// 		}
//
// 		foreach($meeting_params as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_CRM_MEETING."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_CRM_MEETING,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_CRM_MEETING."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_CRM_MEETING."_".$type_id),
// 			));
// 		}
// 		foreach($time_types as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_CRM_MEETING."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_CRM_MEETING,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_CRM_MEETING."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_CRM_MEETING."_".$type_id),
// 			));
// 		}
//
// 		foreach($task_params as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_TASK."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_TASK,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_TASK."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_TASK."_".$type_id),
// 			));
// 		}
// 		foreach($time_types as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_TASK."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_TASK,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_TASK."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_TASK."_".$type_id),
// 			));
// 		}
//
// 		foreach($bugs_params as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_BUG."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_BUG,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_BUG."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_BUG."_".$type_id),
// 			));
// 		}
// 		foreach($time_types as $type_id => $type)
// 		{
// 			if (isset($_GET["st"]) && $_GET["st"] == $parent."_".CL_BUG."_".$type_id)
// 			{
// 				$type = html::bold($type);
// 			}
// 			$tree->add_item($parent."_".CL_BUG,array(
// 				"name" => $type,
// 				"id" => $parent."_".CL_BUG."_".$type_id,
// 				"url" => aw_url_change_var("st", $parent."_".CL_BUG."_".$type_id),
// 			));
// 		}
// 	}

	function _get_tasks_time_tree($arr)
	{
		$tree = $arr["prop"]["vcl_inst"];
		$var = "tm";

		if(!isset($_GET[$var]))
		{
			$_GET[$var] = "currentweek";
		}

		$time_types = array(
			"currentweek" => t("Jooksev n&auml;dal"),
			"currentmonth" => t("Jooksev kuu"),
			"lastmonth" => t("Eelmine kuu"),
		);

		foreach($time_types as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] == $type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item(0,array(
				"name" => $type,
				"id" => $type_id,
				"url" => aw_url_change_var($var, $type_id),
			));
		}

		$type = t("K&otilde;ik perioodid");
		if (isset($_GET[$var]) && $_GET[$var] == "all")
		{
			$type = html::bold($type);
		}
		$tree->add_item(0,array(
			"name" => $type,
			"id" => "undone",
			"url" => aw_url_change_var($var, "all"),
		));
	}


	function _get_tasks_type_tree($arr)
	{
		$tree = $arr["prop"]["vcl_inst"];
		$var = "tf";

		$bug_inst = new bug();

		if(!isset($_GET[$var]))
		{
			$_GET[$var] = "all_undone";
		}

		$types = array(
			CL_BUG => t("&Uuml;lesanne"),
			CL_TASK => t("Toimetus"),
			CL_CRM_MEETING => t("Kohtumine"),
			CL_CRM_CALL => t("K&otilde;ne"),
			"all" => t("K&otilde;ik t&uuml;&uuml;bid")
		);

 		$call_params = array(
 			"done" => t("Tehtud"),
 			"planned" => t("Plaanis"),
 		);

 		$meeting_params = array(
 			"past" => t("Toimunud"),
 			"planned" => t("Tulekul"),
 		);

 		$task_params = array(
 			"done" => t("Tehtud"),
 			"undone" => t("Tegemata"),
 			"overdeadline" => t("&Uuml;le t&auml;htaja"),
 		);

 		$bugs_params = array(
 			"done" => t("L&otilde;petatud"),
 			"undone" => t("Pooleli"),
 			"overdeadline" => t("&Uuml;le t&auml;htaja"),
 		);

		foreach($types as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] == $type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item(0,array(
				"name" => $type,
				"id" => $type_id,
				"url" => aw_url_change_var($var, $type_id),
				"iconurl" => icons::get_icon_url($type_id),
			));
		}

		foreach($call_params as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] == CL_CRM_CALL."_".$type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item(CL_CRM_CALL,array(
				"name" => $type,
				"id" => CL_CRM_CALL."_".$type_id,
				"url" => aw_url_change_var($var, CL_CRM_CALL."_".$type_id),
			));
		}

		foreach($meeting_params as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] == CL_CRM_MEETING."_".$type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item(CL_CRM_MEETING,array(
				"name" => $type,
				"id" => CL_CRM_MEETING."_".$type_id,
				"url" => aw_url_change_var($var,CL_CRM_MEETING."_".$type_id),
			));
		}

		foreach($task_params as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] == CL_TASK."_".$type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item(CL_TASK,array(
				"name" => $type,
				"id" => CL_TASK."_".$type_id,
				"url" => aw_url_change_var($var, CL_TASK."_".$type_id),
			));
		}

		foreach($bugs_params as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] == CL_BUG."_".$type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item(CL_BUG,array(
				"name" => $type,
				"id" => CL_BUG."_".$type_id,
				"url" => aw_url_change_var($var, CL_BUG."_".$type_id),
			));
		}
		foreach($bug_inst->bug_statuses as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] == CL_BUG."_".$type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item(CL_BUG,array(
				"name" => $type,
				"id" => CL_BUG."_".$type_id,
				"url" => aw_url_change_var($var, CL_BUG."_".$type_id),
			));
		}

		foreach($task_params as $type_id => $type)
		{
			if (isset($_GET[$var]) && $_GET[$var] === "all_".$type_id)
			{
				$type = html::bold($type);
			}
			$tree->add_item("all",array(
				"name" => $type,
				"id" => "all_".$type_id,
				"url" => aw_url_change_var($var, "all_".$type_id),
			));
		}
	}

	function _get_tasks_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$var = "st";
		if(!isset($_GET[$var]))
		{
			$_GET[$var] = "my";
		}

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "co_tasks_tree",
		));


		$name = t("Minu tegevused");
		$id = "my";
		if (!isset($_GET[$var]) || $_GET[$var] == $id)
		{
			$name = html::bold($name);
		}
		$tv->add_item(0,array(
			"name" => $name,
			"id" => $id,
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

//		$this->_add_leafs_to_tasks_tree($tv, "my");

		$tv->add_item(0,array(
			"name" => t("Projektijuht"),
			"id" => "prman",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		$bill_stats = new crm_company_bills_impl();

		foreach($bill_stats->all_project_managers()->names() as $id => $name)
		{
			if(!$name)
			{
				continue;
			}

			if (isset($_GET[$var]) && $_GET[$var] === "prman_".$id)
			{
				$name = html::bold($name);
			}

			$tv->add_item("prman",array(
				"name" => $name,
				"id" => "prman".$id,
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => aw_url_change_var($var, "prman_".$id),
			));
//			$this->_add_leafs_to_tasks_tree($tv, "prman".$id);

		}

		$tv->add_item(0,array(
			"name" => t("Kliendihaldur"),
			"id" => "custman",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));

		foreach($bill_stats->all_client_managers()->names() as $id => $name)
		{
			if(!$name)
			{
				continue;
			}
			if (isset($_GET[$var]) && $_GET[$var] === "custman_".$id)
			{
				$name = html::bold($name);
			}
			$tv->add_item("custman",array(
				"name" => $name,
				"id" => "custman".$id,
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => aw_url_change_var($var, "custman_".$id),
			));
//			$this->_add_leafs_to_tasks_tree($tv, "custman".$id);

		}

		$tv->add_item(0,array(
			"name" => t("Klient"),
			"id" => "cust",
//			"url" => aw_url_change_var($var, $stat_id+10),
		));
		$customers_by_1_letter = array();
		$customer_names = $this->all_customers()->names();
		asort($customer_names);
		foreach($customer_names as $customer_id => $customer_name)
		{
			if(!$customer_name)
			{
				continue;
			}
			$customers_by_1_letter[substr($customer_name,0,1)][$customer_id] = $customer_name;
		}

		foreach($customers_by_1_letter as $letter1 => $customers)
		{
			$name = $letter1 ." (".sizeof($customers).")";
			if (isset($_GET[$var]) && $_GET[$var] === "cust_".$letter1)
			{
				$name = html::bold($name);
			}
			$tv->add_item("cust",array(
				"name" => $name,
				"id" => "cust".$letter1,
			//	"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				"url" => aw_url_change_var($var, "cust_".$letter1),
			));

			foreach($customers as $id => $name)
			{
				if (isset($_GET[$var]) && $_GET[$var] === "cust_".$id)
				{
					$name = html::bold($name);
				}
				$tv->add_item("cust".$letter1,array(
					"name" => $name,
					"id" => "cust".$id,
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
					"url" => aw_url_change_var($var, "cust_".$id),
				));
//				$this->_add_leafs_to_tasks_tree($tv, "cust".$id);
			}
		}

		$name = t("K&ouml;ik tegevused");
		$id = "all";
		if (isset($_GET[$var]) && $_GET[$var] == $id)
		{
			$name = html::bold($name);
		}
		$tv->add_item(0,array(
			"name" => $name,
			"id" => $id,
			"url" => aw_url_change_var($var, $id),
		));
 	}

	private function all_customers()
	{
		$ol = new object_list();

		$filter = array(
			"class_id" => CL_BUG,
			"CL_BUG.customer" =>  new obj_predicate_compare(obj_predicate_compare::GREATER, 0)
		);
		$t = new object_data_list(
			$filter,
			array(
				CL_CRM_BILL => array(new obj_sql_func(OBJ_SQL_UNIQUE, "customer", "aw_bugs.customer"))
			)
		);
		$ol->add($t->get_element_from_all("customer"));//FIXME: No access to load object with id '118369'.

		return $ol;
	}

	function _get_activity_stats_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(
			array(CL_CRM_ACTIVITY_STATS_TYPE),
			$arr["obj_inst"]->id(),
			71 /* RELTYPE_ACTIVITY_STATS_TYPE */
		);
		$tb->add_delete_button();
	}

	function _get_activity_stats_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ACTIVITY_STATS_TYPE"))),
			array("name", "created", "createdby"),
			CL_CRM_ACTIVITY_STATS_TYPE
		);
	}
}
