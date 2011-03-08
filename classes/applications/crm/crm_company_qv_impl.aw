<?php

class crm_company_qv_impl extends class_base
{
	function crm_company_qv_impl()
	{
		$this->init("crm");
	}

	function _init_qv_t($t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "parts",
			"caption" => t("Osalejad"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "hrs",
			"caption" => t("Tunde"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center"
		));
	}

	function _get_qv_t($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_qv_t($t);

		$this->hrs_total = 0;
		$this->hrs_on_bill = 0;
		$this->hrs_cl = 0;
		$this->sum = 0;
		$this->bills_sum = 0;
		$this->done_sum = 0;
		$this->bills_paid_sum = 0;

		$r = array();
		if(!empty($arr["request"]["between"]))
		{
			$time_filt = $arr["request"]["between"];
		}
		else
		{
			$r["stats_s_from"] = !empty($arr["request"]["stats_s_from"]) ? date_edit::get_timestamp($arr["request"]["stats_s_from"]) : 0;
			$r["stats_s_to"] = !empty($arr["request"]["stats_s_to"]) ? date_edit::get_timestamp($arr["request"]["stats_s_to"]) : 0;
			if ($r["stats_s_from"] > 1 && $r["stats_s_to"])
			{
				$time_filt = new obj_predicate_compare(OBJ_COMP_BETWEEN, $r["stats_s_from"], $r["stats_s_to"]);
			}
			else
			if ($r["stats_s_from"] > 1)
			{
				$time_filt = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["stats_s_from"]);
			}
			else
			if ($r["stats_s_to"] > 1)
			{
				$time_filt = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $r["stats_s_to"]);
			}
			else
			{
				$time_filt = null;
			}
		}

		// projs
		if (!empty($arr["proj"]))
		{
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_ORDERER" => $arr["obj_inst"]->id(),
				"sort_by" => "aw_deadline desc",
				"state" => "%",
				"oid" => $arr["proj"]
			));
			$pd = html::bold(t("Projektid"));
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_ORDERER" => $arr["obj_inst"]->id(),
				"sort_by" => "aw_deadline desc",
				"state" => "%",
//				"limit" => 5
			));
			$pd = "<b>" . t("Projektid (5 v&auml;rskemat)") . "</b>";
		}
		$pi = new project();
		$t_i = new task();
		$stat = new crm_company_stats_impl();

		$ount = 0;
		foreach($ol->arr() as $o)
		{
			if($count > 5)
			{
				break;
			}
			$parts = array();

			foreach($o->connections_from(array(
				"type" => "RELTYPE_PARTICIPANT",
			)) as $c)
			{
				$parts[] = html::obj_change_url($c->to());
			}

			$sum = 0;
			$hrs = 0;

			// get all tasks for that project and calc sum and hrs
			$t_ol = $o->get_tasks(array(
				"from" => $r["stats_s_from"],
				"to" => $r["stats_s_to"],
			));

/*
			$t_ol = new object_list(array(
				"class_id" => CL_TASK,
				"project" => $o->id(),
				"brother_of" => new obj_predicate_prop("id"),

			));
*/
			foreach($t_ol->arr() as $task)
			{
				foreach($t_i->get_task_bill_rows($task, false) as $row)
				{
					if ($row["date"] < $r["stats_s_from"] || ($row["date"] > $r["stats_s_to"] && $row["stats_s_to"] > 100))
					{
						continue;
					}
					$sum += $row["sum"];
					$hrs += $row["amt"];
				}
			}

			$hrs += $o->get_bugs_time($r["stats_s_from"],$r["stats_s_to"]);
			$date = !($o->prop("start") || $o->prop("end")) ? date("d.m.Y", $o->created()): date("d.m.Y", $o->prop("start")).($o->prop("end") > 100 ? " - ".date("d.m.Y", $o->prop("end")) : "");
			$t->define_data(array(
				"date" => $date,
				"name" => html::obj_change_url($o),
				"parts" => join(", ",array_unique($parts)),
				"hrs" => $stat->hours_format($hrs),//number_format($hrs, 3, ',', ''),
				"sum" => number_format($o->prop("proj_price"), 2, ',', ''),
				"grp_desc" => $pd,
				"grp_num" => 1,
				"state" => $pi->states[$o->prop("state")],
				"icon" => icons::get_icon($o)
			));
			$count++;
		}

		if ($ol->count() == 0)
		{
			$t->define_data(array(
				"date" => "",
				"name" => t("Valitud ajavahemikus ei ole &uuml;htegi projekti!"),
				"parts" => "",
				"grp_desc" => "",
				"grp_num" => 3,
			));
		}

		// tasks
		if (isset($arr["tasks"]))
		{
			if(!sizeof($arr["tasks"]->ids()))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list(array(
					"class_id" => CL_TASK,
					"customer" => $arr["obj_inst"]->id(),
					"sort_by" => "deadline desc",
					"deadline" => "%",
					"oid" => $arr["tasks"]->ids(),
					"brother_of" => new obj_predicate_prop("id")
				));

				// also add call/meeting/offer by range
				$filt = array(
					"class_id" => array(CL_CRM_MEETING, CL_CRM_CALL, CL_CRM_OFFER),
					"customer" => $arr["obj_inst"]->id(),
					"brother_of" => new obj_predicate_prop("id")
				);

				$r = array();
				$r["stats_s_from"] = date_edit::get_timestamp($arr["request"]["stats_s_from"]);
				$r["stats_s_to"] = date_edit::get_timestamp($arr["request"]["stats_s_to"]);


				$filt["start1"] = $time_filt;
				$ol2 = new object_list($filt);
				$ol->add($ol2);
			}
			$grpd = "<b>" . t("Tegevused") . "</b>";
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_TASK,
				"customer" => $arr["obj_inst"]->id(),
				"sort_by" => "deadline desc",
				"deadline" => "%",
	//			"limit" => 10
			));
			$grpd = "<b>" . t("Tegevused (10 v&auml;rskemat)") . "</b>";
		}

		$count = 0;
		foreach($ol->arr() as $o)
		{
			$sum = 0;
			$hrs = 0;
			foreach($t_i->get_task_bill_rows($o, false) as $row)
			{
				if ($row["date"] < $r["stats_s_from"] || ($row["date"] > $r["stats_s_to"] && $row["stats_s_to"] > 100))
				{
					continue;
				}
				$sum += str_replace(",", "",$row["sum"]);
				$hrs += $row["amt"];
				$this->hrs_total += $row["amt_real"];
				$this->hrs_cl += $row["amt"];
				$this->sum += $row["sum"];
				$this->done_sum += $row["sum"];
			}
			if ($o->class_id() != CL_TASK)
			{
				$hrs += $o->prop("time_real");
				$this->hrs_total += $o->prop("time_real");
				$this->hrs_cl += $o->prop("time_to_cust");
				$this->sum += str_replace(",",".", $o->prop("time_real")) * $o->prop("hr_price");
				$sum += str_replace(",",".", $o->prop("time_real")) * $o->prop("hr_price");
			}
			if($count > 9)
			{
				continue;
			}

			$parts = array();
			$pol = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"CL_CRM_PERSON.RELTYPE_PERSON_TASK" => $o->id(),//kui k6ik panen, jookseb miskit kokku
//				new object_list_filter(array(
//					"logic" => "OR",
//					"conditions" => array(
//						"CL_CRM_PERSON.RELTYPE_PERSON_MEETING" => $o->id(),
//						"CL_CRM_PERSON.RELTYPE_PERSON_CALL" => $o->id(),
//						"CL_CRM_PERSON.RELTYPE_PERSON_TASK" => $o->id(),
//					)
//				)),
			));

			foreach($pol->arr() as $person)
			{
				$parts[] = html::obj_change_url($person->id());
			}

			$end = "";
			if ($o->prop("end") > $o->prop("start1"))
			{
				$end = " - ".date("d.m.Y", $o->prop("end"));
			}
			$t->define_data(array(
				"icon" => icons::get_icon($o),
				"date" => date("d.m.Y", $o->prop("start1")).$end,
				"name" => html::obj_change_url($o,($o->name()?$o->name():t("Nimetu"))),
				"parts" => join(", ", $parts),
				"hrs" => number_format($hrs, 3, ',', ''),
				"sum" => number_format($sum, 2, ',', ''),
				"grp_desc" => $grpd,
				"grp_num" => 2,
				"state" => ($o->flags() & 8)  == 8 ? t("Tehtud") : t("T&ouml;&ouml;s"),
				"sb" => $o->prop("start1")
			));
			$count++;
		}

		if ($ol->count() == 0)
		{
			$t->define_data(array(
				"date" => "",
				"name" => t("Valitud ajavahemikus ei ole &uuml;htegi tegevust!"),
				"parts" => "",
				"grp_desc" => "",
				"grp_num" => 3,
			));
		}

		// bugs
		$bug_count = 0;
		$bi = new bug();
		$ol = new object_list(array(
			"class_id" => CL_BUG,
			"customer" => $arr["obj_inst"]->id(),
//			"sort_by" => "objects.created desc",
			"who" => "%",
//			"limit" => 10
			"CL_BUG.RELTYPE_COMMENT.created" => $time_filt,
		));
		$ol->sort_by_cb(array($this, "__bug_sorter"));

		$bd = "<span style='font-size: 0px;'>y</span><b>" . t("Bugid (10 v&auml;rskemat)") . "</b>";

		foreach($ol->arr() as $o)
		{
			$real_time = $o->get_bug_comments_time($r["stats_s_from"],$r["stats_s_to"]);
			$this->hrs_total += $real_time;
			$this->hrs_cl += $o->prop("num_hrs_to_cust");
			if($bug_count > 10)
			{
				continue;
			}

//			$parts = array();
//			foreach((array)$o->prop("participants") as $_p)
//			{
//				$parts[] = html::obj_change_url($_p);
//			}

			$parts = "";
			if($this->can("view" , $o->prop("who")))
			{
				$parts = html::obj_change_url($o->prop("who"));
			}

			foreach($rows as $row)
			{
				$hrs += str_replace(",", ".",$row["amt"]);
			}

			$c_time = $o->get_last_comment_time();

			$t->define_data(array(
				"date" => $c_time > 100 ? date("d.m.Y", $c_time) : "",
				"name" => html::get_change_url($o->id(), array("return_url" => get_ru(), "group" => "preview"), ($o->name()?$o->name():t("Nimetu"))),
				"parts" => $parts,
				"hrs" => number_format($real_time, 2, ',', ''),
	//			"sum" => number_format($sum, 2, ',', ''),
				"grp_desc" => $bd,
				"grp_num" => 4,
				"state" => $bi->bug_statuses[$o->prop("bug_status")],
				"icon" => icons::get_icon($o),
				"sb" => $c_time
			));
			$bug_count++;
		}

		if ($ol->count() == 0)
		{
			$t->define_data(array(
				"date" => "",
				"name" => t("Valitud ajavahemikus ei ole &uuml;htegi bugi!"),
				"parts" => "",
				"grp_desc" => $bd,
				"grp_num" => 4,
			));
		}

		// bills
		$count = 0;
		if (isset($arr["tasks"]))
		{
			$f = array(
				"class_id" => CL_CRM_BILL,
				"customer" => $arr["obj_inst"]->id(),
				"sort_by" => "aw_crm_bill.aw_date desc",
				"bill_no" => "%",
				"CL_CRM_BILL.RELTYPE_TASK" => $ol->ids() // only from the task list for this co
			);
			$ol = new object_list($f);
			$bd = "<span style='font-size: 0px;'>y</span><b>" . t("Arved") . "</b>";
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_BILL,
				"customer" => $arr["obj_inst"]->id(),
				"sort_by" => "aw_crm_bill.aw_date desc",
				"bill_no" => "%",
//				"limit" => 10
			));
			$bd = "<span style='font-size: 0px;'>y</span><b>" . t("Arved (10 v&auml;rskemat)") . "</b>";
		}
		foreach($ol->arr() as $o)
		{
			$bi = $o->instance();
			$sum = $bi->get_sum($o);
			$rows = $bi->get_bill_rows($o);
			$hrs = 0;
			foreach($rows as $row)
			{
				$hrs += str_replace(",", ".",$row["amt"]);
			}

			$this->bills_sum += str_replace(",", "", $sum);
			$this->hrs_on_bill += $hrs;
			if ($o->prop("state") == 2)
			{
				$this->bills_paid_sum += $sum;
			}

			if($count > 9)
			{
				continue;
			}

			$parts = array();
			foreach((array)$o->prop("impl") as $_p)
			{
				$parts[] = html::obj_change_url($_p);
			}

			$t->define_data(array(
				"date" => $o->prop("bill_date") > 100 ? date("d.m.Y", $o->prop("bill_date")) : "",
				"name" => html::get_change_url($o->id(), array("return_url" => get_ru(), "group" => "preview"), ($o->name()?$o->name():t("Nimetu"))),
				"parts" => join(", " , $parts),
				"hrs" => number_format($hrs, 3, ',', ''),
				"sum" => number_format($sum, 2, ',', ''),
				"grp_desc" => $bd,
				"grp_num" => 3,
				"state" => $bi->states[$o->prop("state")],
				"icon" => icons::get_icon($o),
				"sb" => $o->prop("bill_date"),
			));

			$count++;
		}
		if ($ol->count() == 0)
		{
			$t->define_data(array(
				"date" => "",
				"name" => t("Valitud ajavahemikus ei ole &uuml;htegi arvet!"),
				"parts" => "",
				"grp_desc" => $bd,
				"grp_num" => 3,
			));
		}

		$t->sort_by(array(
			"rgroupby" => array("grp_num" => "grp_desc"),
			"sorder" => "asc",
			"field" => array("sb", "grp_num")
		));
		$t->set_sortable(false);
	}

	function __bug_sorter($a , $b)
	{
		$timea = $a->get_last_comment_time();
		$timeb = $b->get_last_comment_time();
		return $timeb - $timea;
	}

	function _get_qv_cust_inf($arr)
	{
		$this->read_template("qv.tpl");

		$this->_insert_gen_vars();

		$o = $arr["obj_inst"];

		$cp = "";
		$u = get_instance(CL_USER);
		$cur_p = obj($u->get_current_person());
		$conns = $cur_p->connections_from(array(
			"type" => "RELTYPE_IMPORTANT_PERSON",
		));

		$i = new crm_company();
		$all_persons = array();
		$i->get_all_workers_for_company($arr["obj_inst"], $all_persons);

		// leave only conns that point to people in this company
		foreach($conns as $idx => $c)
		{
			if (!isset($all_persons[$c->prop("to")]))
			{
				unset($conns[$idx]);
			}
		}

		foreach($conns as $c)
		{
			$_cp = $c->to();
			$cp .= $_cp->name().", ".$_cp->prop_str("phone").", ".$_cp->prop_str("email")."<br>";
		}

		$_ev = $o->prop("ettevotlusvorm");
		if ($this->can("view", $_ev))
		{
			$ev = obj($_ev);
			$ev = $ev->prop("shortname");
		}

		$this->_get_qv_t(array(
			"prop" => array(
				"vcl_inst" => new vcl_table(),
			),
			"proj" => isset($arr["proj"]) ? $arr["proj"] : null,
			"obj_inst" => $arr["obj_inst"],
			"tasks" => isset($arr["tasks"]) ? $arr["tasks"] : null,
			"request" => isset($arr["request"]) ? $arr["request"] : null
		));

		$tg = $_GET;

		if (empty($tg["stats_s_to"]))
		{
			$tg["stats_s_to"] = time();
		}
		else
		{
			$tg["stats_s_to"] = date_edit::get_timestamp($tg["stats_s_to"]);
		}

		if (empty($tg["stats_s_from"]))
		{
			$tg["stats_s_from"] = $o->prop("cust_contract_date");
		}
		else
		{
			$tg["stats_s_from"] = date_edit::get_timestamp($tg["stats_s_from"]);
		}

		$ts = date("d.m.Y", $tg["stats_s_from"])." - ".date("d.m.Y", $tg["stats_s_to"]);
		$this->vars(array(
			"name" => $o->name()." ".$ev,
			"code" => $o->prop("code"),
			"reg_code" => $o->prop("reg_nr"),
			"kmk_nr" => $o->prop("tax_nr"),
			"desc" => $o->prop("tegevuse_kirjeldus"),
			"trademarks" => $o->prop("kaubamargid"),
			"cust_contract_date" => date("d.m.Y", $o->prop("cust_contract_date")),
			"cust_contract_creator" => html::obj_change_url($o->prop("cust_contract_creator")),
			"referal_type" => $o->prop_str("referal_type"),
			"client_manager" => $o->prop_str("client_manager"),
			"address" => $o->prop_str("contact.name"),
			"phone" => $o->prop_str("phone_id.name"),
			"fax" => $o->prop_str("telefax_id.name"),
			"email" => $o->prop_str("email_id.mail"),
			"web" => $o->prop_str("url_id.name"),
			"contact_p" => $cp,
			"bills_in_sum" => number_format($this->bills_sum, 2, ',', ''),
			"done_sum" => number_format($this->sum, 2, ',', ''),
			"hrs_on_bill" =>  number_format($this->hrs_on_bill, 3, ',', ''),
			"total_work_hrs" =>  number_format($this->hrs_total, 3, ',', ''),
			"hrs_to_cust" => number_format($this->hrs_cl, 3, ',', ''),
			"timespan" => $ts
		));
		return $arr["prop"]["value"] = $this->parse();
	}

	function _insert_gen_vars()
	{
		$this->vars(array(
			"cust_gen_data" => t("Kliendi &uuml;ldandmed"),
			"cust_name" => t("Nimetus"),
			"start_date" => t("Alguskuup&auml;ev"),
			"code_str" => t("Kood"),
			"creat_str" => t("Suhte looja"),
			"reg_code_str" => t("Registrikood"),
			"crel_str" => t("Kliendisuhe"),
			"ref_str" => t("Sissetuleku meetod"),
			"kmk_str" => t("KMK nr"),
			"cm_str" => t("Kliendihaldur"),
			"desc_str" => t("Tegevuse kirjeldus"),
			"trm_str" => t("Kaubam&auml;rgid"),
			"cd_str" => t("Kontaktandmed"),
			"cp_str" => t("Kontaktisikud"),
			"adr_str" => t("Aadress"),
			"ph_str" => t("Telefon"),
			"fx_str" => t("Faks"),
			"em_str" => t("E-post"),
			"w_str" => t("WWW"),
			"inc_str" => t("Tulud"),
			"ts_str" => t("Ajavahemikul"),
			"twh_str" => t("T&ouml;&ouml;tunde kokku"),
			"hob_str" => t("Arvele l&auml;inud t&ouml;&ouml;tunde"),
			"d_str" => t("Tehtud t&ouml;id summas"),
			"pb_str" => t("Esitatud arveid summas"),
			"hcust_str" => t("T&ouml;&ouml;tunde kliendile"),
		));
	}
}
