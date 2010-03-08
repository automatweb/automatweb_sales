<?php
/*
@classinfo  maintainer=robert
*/

class bt_stat_impl extends core
{
	function bt_stat_impl()
	{
		$this->init();
	}

	function _init_stat_hrs_ov_t(&$t)
	{
		$t->define_field(array(
			"name" => "p",
			"caption" => t("Isik"),
			"align" => "center",
			"sortable" => 1
		));
		for($i = 1; $i <= 12; $i++)
		{
			$t->define_field(array(
				"name" => "m".sprintf("%02d", $i),
				"caption" => aw_locale::get_lc_month($i),
				"align" => "center",
				"sortable" => 1
			));
		}
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"sortable" => 1,
			"type" => "int",
			"numeric" => 1
		));
	}

	function _get_stat_hrs_overview($arr)
	{
		// table year is group, month is col
		// row is person
		$req_start = empty($arr["request"]["stat_hrs_start"]) ? mktime(0, 0, 0, date("n"), 1, date("Y"), 1) : mktime(0, 0, 0, $arr["request"]["stat_hrs_start"]["month"], $arr["request"]["stat_hrs_start"]["day"], $arr["request"]["stat_hrs_start"]["year"], 1);
		$req_end = empty($arr["request"]["stat_hrs_end"]) ? time() + 86400 : mktime(23, 59, 59, $arr["request"]["stat_hrs_end"]["month"], $arr["request"]["stat_hrs_end"]["day"], $arr["request"]["stat_hrs_end"]["year"], 1);
		$time_constraint = null;

		$co_conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_OWNER",
		));
		foreach($co_conn as $c)
		{
			$cos[$c->prop("to")] = $c->prop("to");
		}
		if (2 < $req_start and $req_start < $req_end)
		{
			$time_constraint = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $req_start, $req_end);
		}
		$stat_hrs = array();
		if($arr["request"]["stat_hr_bugs"] || !$arr["request"]["stat_hrs_end"])
		{
			$ol = new object_list(array(
				"class_id" => CL_BUG_COMMENT,
				"lang_id" => array(),
				"site_id" => array(),
				"add_wh" => new obj_predicate_not(0),
				"created" => $time_constraint
			));
			
	
			foreach($ol->arr() as $o)
			{
				$stat_hrs[$o->createdby()][] = $o;
			}
		}
/*		$types = $this->get_event_types();
		foreach($types as $type)
		{
			if($arr["request"]["stat_hr_".$type["rname"]] || !$arr["request"]["stat_hrs_end"])
			{
				$ol = new object_list(array(
					"class_id" => $type["class_id"],
					"lang_id" => array(),
					"site_id" => array(),
					"is_work" => 1,
					"start1" => $time_constraint,
					"brother_of" => new obj_predicate_prop("id")
				));
				foreach($ol->arr() as $o)
				{
					if(!$o->prop($type["timevar"]))
					{
						continue;
					}
					$tp = $type["types"];
					foreach($o->connections_to(array("type" => $tp)) as $co)
					{
						$pi = get_instance(CL_CRM_PERSON);
						$po = obj($co->conn["from"]);
						$u = $pi->has_user($po);
						if($u !== false)
						{
							$uname = $u->name();
							$stat_hrs[$uname][] = $o;
						}
					}
				}
			}
		}
*/
		$classes = array();
		if($arr["request"]["stat_hr_tasks"]) $classes[] = CL_TASK;
		if($arr["request"]["stat_hr_calls"]) $classes[] = CL_CRM_CALL;
		if($arr["request"]["stat_hr_meetings"]) $classes[] = CL_CRM_MEETING;
		if($arr["request"]["stat_hr_bugs"]) $classes[] = CL_BUG;

		$ol = new object_list(array(
			"class_id" => CL_TASK_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"done" => 1,
			"date" => $time_constraint,
			"task.class_id" => $classes,//selle tahaks tegelikult et tuleks parent.class_id
		));
		foreach($ol->arr() as $o)
		{
			if(!$o->prop("time_real"))
			{
				continue;
			}
			$tp = $type["types"];
			$impl = $o->prop("impl");
			foreach(safe_array($impl) as $pid)
			{
				$pi = get_instance(CL_CRM_PERSON);
				$po = obj($pid);
				$u = $pi->has_user($po);
				if($u !== false)
				{
					$uname = $u->name();
					$stat_hrs[$uname][] = $o;
				}
			}
		}

		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stat_hrs_ov_t($t);
		foreach($stat_hrs as $uid => $coms)
		{
			$u = get_instance(CL_USER);
			$p = $u->get_person_for_uid($uid);
			if(is_array($cos) && count($cos) && !$cos[$p->company_id()])
			{
				continue;
			}
			$dmz = array();

			foreach($coms as $com)
			{
				if($com->class_id() == CL_BUG_COMMENT)
				{
					$dmz[date("Y", $com->created())]["m".date("m", $com->created())] += $com->prop("add_wh");
				}
				elseif($com->class_id() == CL_TASK)
				{
					$dmz[date("Y", $com->prop("start1"))]["m".date("m",$com->prop("start1"))] += $com->prop("num_hrs_real");
					
				}
				elseif($com->class_id() == CL_CRM_MEETING || $com->class_id() == CL_CRM_CALL)
				{
					$dmz[date("Y", $com->prop("start1"))]["m".date("m",$com->prop("start1"))] += $com->prop("time_real");
				}
				elseif($com->class_id() == CL_TASK_ROW)
				{
					$dmz[date("Y", $com->prop("date"))]["m".date("m", $com->prop("date"))] += $com->prop("time_real");
				}
			}

			foreach($dmz as $year => $mons)
			{
				$row_sum = 0;

				foreach($mons as $mon => $wh)
				{
					$det_day_start = $det_day_end = null;
					$mon_num = (int)substr($mon, 1);
					if($mon_num == $arr["request"]["stat_proj_start"]["month"] && $year == $arr["request"]["stat_hrs_start"]["year"])
					{
						$det_day_start = $arr["request"]["stat_hrs_start"]["day"];
					}
					if($mon_num == $arr["request"]["stat_hrs_end"]["month"] && $year == $arr["request"]["stat_hrs_end"]["year"])
					{
						$det_day_end = $arr["request"]["stat_hrs_end"]["day"];
					}
					$mons[$mon] = html::href(array(
						"url" => aw_url_change_var(array(
							"det_uid" => $uid,
							"det_year" => $year,
							"det_mon" => $mon_num,
							"det_day_start" => $det_day_start,
							"det_day_end" => $det_day_end,
						)),
						"caption" => number_format($wh, 2, ".", " ")
					));
					$row_sum += $wh;
				}

				$mons["p"] = html::obj_change_url($p);
				$mons["year"] = $year;

				if ($wh>0)
				{
					$mons["sum"] = number_format($row_sum, 2, ".", " ");
					$t->define_data($mons);
				}
			}
		}

		$t->set_rgroupby(array("year" => "year"));
		$t->set_caption(t("T&ouml;&ouml;tundide statistika aastate ja kuude kaupa"));
		$t->set_default_sortby("sum");
		$t->set_default_sorder("desc");
	}

	function _init_stat_det_t(&$t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "bug",
			"caption" => t("Bugi"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Kuup&auml;ev"),
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y / H:i",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "wh",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => 1
		));
		$t->sort_by();
		$t->set_default_sortby("time");
	}

	function _get_stat_hrs_detail($arr)
	{
		if (!$arr["request"]["det_uid"] || !$arr["request"]["det_year"] || !$arr["request"]["det_mon"])
		{
			return PROP_IGNORE;
		}

		// list all bugs and their times for that person for that time
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stat_det_t($t);
		if($dds = $arr["request"]["det_day_start"])
		{
			$startday = $dds;
		}
		else
		{
			$startday = 1;
		}
		if($eds = $arr["request"]["det_day_end"])
		{
			$endday = $eds;
			$endmonth = $arr["request"]["det_mon"];
		}
		else
		{
			$endday = 0;
			$endmonth = $arr["request"]["det_mon"]+1;
		}
		$fancy_filter = new obj_predicate_compare(
			OBJ_COMP_BETWEEN_INCLUDING,
			mktime(0,0,0, $arr["request"]["det_mon"], $startday, $arr["request"]["det_year"]),
			mktime(23,59,59, $endmonth, $endday, $arr["request"]["det_year"])
		);

		if($arr["request"]["stat_hr_bugs"] || !$arr["request"]["stat_hrs_end"])
		{
			$ol = new object_list(array(
				"class_id" => CL_BUG_COMMENT,
				"lang_id" => array(),
				"site_id" => array(),
				"created" => $fancy_filter,
				"createdby" => $arr["request"]["det_uid"]
			));
	
			$bugs = array();
			foreach($ol->arr() as $com)
			{
				$bugs[$com->parent()]["hrs"] += $com->prop("add_wh");
				if(!$bugs[$com->parent()]["lastdate"] || $bugs[$com->parent()]["lastdate"] > $com->created())
				$bugs[$com->parent()]["lastdate"] = $com->created();
			}
		}
//		$types = $this->get_event_types();
		
		$ui = get_instance(CL_USER);
		$p = $ui->get_person_for_uid($arr["request"]["det_uid"]);
		$startd = mktime(0,0,0, $arr["request"]["det_mon"], $startday, $arr["request"]["det_year"]);
		$endd = mktime(23,59,59, $arr["request"]["det_mon"]+1, $endday, $arr["request"]["det_year"]);
/*		foreach($types as $type)
		{
			if($arr["request"]["stat_hr_".$type["rname"]] || !$arr["request"]["stat_hrs_end"])
			{
				$c = new connection();
				$list = $c->find(array(
					"to.class_id" => $type["class_id"],
					"from.class_id" => CL_CRM_PERSON,
					"type" => $type["types"],
					"from" => $p->id()
				));
				foreach($list as $item)
				{
					$o = obj($item["to"]);
					if($o->prop("is_work") && $o->prop("start1") > $startd && $o->prop("start1")<$endd)
					{
						$bugs[$item["to"]]["hrs"] += $o->prop($type["timevar"]);
						$bugs[$item["to"]]["lastdate"] = ($o->class_id() == CL_BUG) ? $o->modified() : $o->prop("start1");
					}
				}
			}
		}*/

		$classes = array();
		if($arr["request"]["stat_hr_tasks"]) $classes[] = CL_TASK;
		if($arr["request"]["stat_hr_calls"]) $classes[] = CL_CRM_CALL;
		if($arr["request"]["stat_hr_meetings"]) $classes[] = CL_CRM_MEETING;
		if($arr["request"]["stat_hr_bugs"]) $classes[] = CL_BUG;

		$ol = new object_list(array(
			"class_id" => CL_TASK_ROW,
			"impl" => $p->id(),
			"site_id" => array(),
			"lang_id" => array(),
			"date" => $fancy_filter,
			"task.class_id" => $classes,
		));
		foreach($ol->arr() as $oid => $o)
		{
			if($o->prop("parent.class_id") != CL_TASK)
			{
				$bugs[$o->parent()]["hrs"] += $o->prop("time_real");
				$bugs[$o->parent()]["lastdate"] = $o->prop("date");
			}
			else
			{
				$bugs[$oid]["hrs"] += $o->prop("time_real");
				$bugs[$oid]["lastdate"] = $o->prop("date");
			}
		}

		$this->_insert_det_data_from_arr($bugs, &$t);

		$u = get_instance(CL_USER);
		$p = $u->get_person_for_uid($arr["request"]["det_uid"]);
		if($dds = $arr["request"]["det_day_start"])
		{
			$startday = $dds;
		}
		else
		{
			$startday = 1;
		}
		if($eds = $arr["request"]["det_day_end"])
		{
			$endday = $eds;
			$endmonth = $arr["request"]["det_mon"];
		}
		else
		{
			$endday = 0;
			$endmonth = $arr["request"]["det_mon"]+1;
		}
		$t->set_caption(sprintf(t("%s t&ouml;&ouml;tunnid ajavahemikul %s - %s"),
			$p->name(),
			date("d.m.Y", mktime(0,0,0, $arr["request"]["det_mon"], $startday, $arr["request"]["det_year"])),
			date("d.m.Y", mktime(0,0,0, $endmonth, $endday, $arr["request"]["det_year"]))
		));
	}

	function _insert_det_data_from_arr($arr, $t)
	{
		foreach($arr as $bug => $data)
		{
			$o = obj($bug);
			classload("core/icons");
			if ($data["hrs"] > 0)
			{
				if($o->class_id() == CL_TASK_ROW)
				{
					$conn = $o->connections_to(array(
						"type" => "RELTYPE_ROW",
						"from.class_id" => CL_TASK,
					));
					$tname = null;
					foreach($conn as $c)
					{
						$tname = $c->prop("from.name");
					}
					if(!$tname)
					{
						$tname = $o->name();
					}
					else
					{
						$tname .= t(" rida");
					}
					$name = html::obj_change_url($bug, parse_obj_name($tname));
					$iconurl = icons::get_icon_url(CL_TASK);
				}
				else
				{
					$name = html::obj_change_url($bug);
					$iconurl = icons::get_icon_url($o->class_id());
				}
				$t->define_data(array(
					"icon" => html::img(array("url" => $iconurl)),
					"time" => $data["lastdate"],
					"bug" => $name,
					"wh" => $data["hrs"]
				));
			}
		}
	}

	function get_event_types()
	{
		$types = array(
			0 => array(
				"rname" => "tasks",
				"class_id" => CL_TASK,
				"timevar" => "num_hrs_real",
				"types" => array(10,8)
			),
			1 => array(
				"rname" => "calls",
				"class_id" => CL_CRM_CALL,
				"timevar" => "time_real",
				"types" => 9
			),
			2 => array(
				"rname" => "meetings",
				"class_id" => CL_CRM_MEETING,
				"timevar" => "time_real",
				"types" => 8
			),
		);
		return $types;
	}

	function _init_errs_t(&$t)
	{
		$t->define_field(array(
			"name" => "bug",
			"caption" => t("Bug"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "com",
			"caption" => t("Kommentaar"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "wh",
			"caption" => t("T&ouml;&ouml;tunde"),
			"align" => "center",
		));
	}

	function _get_stat_hrs_errs($arr)
	{
		if (!$arr["request"]["dbg"])
		{
			return PROP_IGNORE;
		}
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_errs_t($t);

		$ol = new object_list(array(
			"class_id" => CL_BUG_COMMENT,
			"lang_id" => array(),
			"site_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"add_wh" => new obj_predicate_compare(OBJ_COMP_LESS, 0)
						)
					)),
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"add_wh" => new obj_predicate_compare(OBJ_COMP_GREATER, 10)
						)
					))
				)
			))
		));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"bug" => html::obj_change_url($o->parent()),
				"com" => html::obj_change_url($o),
				"wh" => $o->prop("add_wh")
			));
		}
		if (!$ol->count())
		{
			return PROP_IGNORE;
		}
	}

	function _init_stat_proj_det(&$t)
	{
		$t->define_field(array(
			"name" => "p",
			"caption" => t("Isik"),
			"align" => "center",
			"sortable" => 1
		));
		for($i = 1; $i < 13; $i++)
		{
			$t->define_field(array(
				"name" => "m".sprintf("%02d", $i),
				"caption" => aw_locale::get_lc_month($i),
				"align" => "center",
				"sortable" => 1
			));
		}
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"sortable" => 1,
			"type" => "int"
		));
	}

	function _get_stat_proj_detail($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stat_proj_det($t);
		$req_start = empty($arr["request"]["stat_proj_hrs_start"]) ? mktime(0, 0, 1, date("m"), 1, date("Y")) : mktime(0, 0, 0, $arr["request"]["stat_proj_hrs_start"]["month"], $arr["request"]["stat_proj_hrs_start"]["day"], $arr["request"]["stat_proj_hrs_start"]["year"], 1);
		$req_end = empty($arr["request"]["stat_proj_hrs_end"]) ? time() + 86400 : mktime(23, 59, 59, $arr["request"]["stat_proj_hrs_end"]["month"], $arr["request"]["stat_proj_hrs_end"]["day"], $arr["request"]["stat_proj_hrs_end"]["year"], 1);
		$time_constraint = null;

		if (2 < $req_start and $req_start < $req_end)
		{
			$time_constraint = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $req_start, $req_end);
		}
		// table year is group, month is col
		// row is person
		$filt = array(
			"class_id" => array(CL_BUG_COMMENT),
			"lang_id" => array(),
			"site_id" => array(),
			"created" => $time_constraint
		);

		if (is_array($arr["request"]["stat_proj_ppl"]) && count($arr["request"]["stat_proj_ppl"]))
		{
			$users = array();
			foreach($arr["request"]["stat_proj_ppl"] as $pers_id)
			{
				if ($this->can("view", $pers_id))
				{
					$po = obj($pers_id);
					$us = $po->instance()->has_user($po);
					if ($us)
					{
						$users[] = $us->prop("uid");
					}
				}
			}
			$filt["createdby"] = $users;
		}
		if($arr["request"]["stat_proj_bugs"] || !$arr["request"]["stat_proj_hrs_end"])
		{
			$ol = new object_list($filt);
			$stat_hrs = array();
			$bugids = array();
			$sum_by_proj = array();
			foreach($ol->arr() as $o)
			{
				$tm = $o->created();
				$stat_hrs[$o->createdby()][] = $o;
				$bugids[$o->parent()] = 1;
			}
	
			if(!$bugids)
			{
				$bugids = array(-1 => -1);
			}
	
			$bug_ol = new object_list(array(
				"oid" => array_keys($bugids),
				"lang_id" => array(),
				"site_id" => array()
			));
			$bug_ol->begin();
	
			foreach($ol->arr() as $com)
			{
				if($com->prop("add_wh"))
				{
					$bug = obj($com->parent());
					$sum_by_proj[$bug->prop("project")] += $com->prop("add_wh");
				}
			}
		}
/*		$types = $this->get_event_types();
		foreach($types as $type)
		{
			if($arr["request"]["stat_proj_".$type["rname"]] || !$arr["request"]["stat_proj_hrs_end"])
			{
				$ol = new object_list(array(
					"class_id" => $type["class_id"],
					"lang_id" => array(),
					"site_id" => array(),
					"is_work" => 1,
					"start1" => $time_constraint,
					"brother_of" => new obj_predicate_prop("id")
				));
				foreach($ol->arr() as $o)
				{
					if(!$o->prop($type["timevar"]))
					{
						continue;
					}
					$projects = array();
					foreach($o->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
					{
						$sum_by_proj[$c->prop("to")] += $o->prop($type["timevar"]);
						$projects[$c->prop("to")] = $c->prop("to");
					}
					$tp = $type["types"];
					foreach($o->connections_to(array("type" => $tp)) as $co)
					{
						$pi = get_instance(CL_CRM_PERSON);
						$po = obj($co->conn["from"]);
						$u = $pi->has_user($po);
						if($u !== false)
						{
							$uname = $u->name();
							$stat_hrs[$uname][] = array(
								"start" => $o->prop("start1"),
								"projects" => $projects,
								"time" => $o->prop($type["timevar"])
							);
						}
					}
				}
			}
		}*/

		$classes = array();
		if($arr["request"]["stat_hr_tasks"]) $classes[] = CL_TASK;
		if($arr["request"]["stat_hr_calls"]) $classes[] = CL_CRM_CALL;
		if($arr["request"]["stat_hr_meetings"]) $classes[] = CL_CRM_MEETING;
		if($arr["request"]["stat_hr_bugs"]) $classes[] = CL_BUG;
		$rows_filter = array(
			"class_id" => CL_TASK_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"done" => 1,
			"date" => $time_constraint,
			"task.class_id" => $classes,
			"time_real" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);
		$ol = new object_list($rows_filter);
		foreach($ol->arr() as $o)
		{
			$tp = $type["types"];
			$impl = $o->prop("impl");
			if (!($task_o = $o->task()))
			{
				continue;
			}
			$projects = array();
			foreach($task_o->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
			{
				$sum_by_proj[$c->prop("to")] += ($task_o->class_id() == CL_BUG) ? $o->prop("time_real") : $task_o->get_time_for_project($c->prop("to"), $o->prop("time_real"));
				$projects[$c->prop("to")] = $c->prop("to");
			}
			foreach($impl as $pid)
			{
				$pi = get_instance(CL_CRM_PERSON);
				$po = obj($pid);
				$u = $pi->has_user($po);
				if($u !== false)
				{
					$uname = $u->name();
					$stat_hrs[$uname][] = array(
						"start" => $o->prop("date"),
						"projects" => $projects,
						"time" => $o->prop("time_real")
					);
				}
				}
		}
		$tot_sum = 0;
		$p2uid = array();
		foreach($stat_hrs as $uid => $coms)
		{
			$u = get_instance(CL_USER);
			$p = $u->get_person_for_uid($uid);
			$p2uid[$p->id()] = $uid;
			foreach($coms as $com)
			{
				if(is_array($com))
				{
					$o = $com["object"];
					foreach($com["projects"] as $proj)
					{
						$dmz["y".date("Y", $com["start"])][$proj][$p->id()]["m".date("m", $com["start"])] += $com["time"];
					}
				}
				elseif($com->class_id() == CL_BUG_COMMENT)
				{
					$bug = obj($com->parent());
					$dmz["y".date("Y", $com->created())][$bug->prop("project")][$p->id()]["m".date("m", $com->created())] += $com->prop("add_wh");
				}
			}
		}
		foreach($dmz as $year=>$projs)
		{
			foreach($projs as $proj => $users)
			{
				if (!$this->can("view", $proj))
				{
					continue;
				}
				foreach($users as $pid=>$mons)
				{
					$p = obj($pid);
					$row_sum = 0;
					foreach($mons as $mon => $wh)
					{
						if(!$wh)
						{
							$mons[$mon] = null;
							continue;
						}
						$det_day_start = $det_day_end = null;
						$mon_num = (int)substr($mon, 1);
						$year_num = (int)substr($year, 1);
						if($mon_num == $arr["request"]["stat_proj_hrs_start"]["month"] && $year == $arr["request"]["stat_proj_hrs_start"]["year"])
						{
							$det_day_start = $arr["request"]["stat_proj_hrs_start"]["day"];
						}
						if($mon_num == $arr["request"]["stat_proj_hrs_end"]["month"] && $year == $arr["request"]["stat_proj_hrs_end"]["year"])
						{
							$det_day_end = $arr["request"]["stat_proj_hrs_end"]["day"];
						}
						$mons[$mon] = html::href(array(
							"url" => aw_url_change_var(array(
								"det_uid" => $p2uid[$p->id()],
								"det_proj" => $proj,
								"det_mon" => $mon_num,
								"det_year" => $year_num,
								"det_day_start" => $det_day_start,
								"det_day_end" => $det_day_end,
							)),
							"caption" => number_format($wh, 2, ".", " ")
						));
						$row_sum += $wh;
					}
					$mons["p"] = html::obj_change_url($p);
					$mons["proj"] = html::obj_change_url($proj)." - ".$sum_by_proj[$proj];
					$mons["year"] = substr($year, 1);
					if ($wh > 0)
					{
						$mons["sum"] = number_format($row_sum, 2, ".", " ");
						$tot_sum += $row_sum;
						$t->define_data($mons);
					}
				}
			}
		}

		$t->set_rgroupby(array("year" => "year", "proj"=>"proj"));
		$t->set_caption(t("T&ouml;&ouml;tundide statistika projektide ja kuude kaupa"));
		$t->set_default_sortby("sum");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$t->set_sortable(false);
		$t->define_data(array("p" => t("<b>Summa</b>"),"sum" => number_format($tot_sum, 2, ".", " ")));
	}

	function _get_stat_proj_detail_b($arr)
	{
		if (!$arr["request"]["det_uid"] || !$arr["request"]["det_proj"] || !$arr["request"]["det_mon"])
		{
			return PROP_IGNORE;
		}

		// list all bugs and their times for that person for that time
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stat_det_t($t);

		if($dds = $arr["request"]["det_day_start"])
		{
			$startday = $dds;
		}
		else
		{
			$startday = 1;
		}
		if($eds = $arr["request"]["det_day_end"])
		{
			$endday = $eds;
			$endmonth = $arr["request"]["det_mon"];
		}
		else
		{
			$endday = 0;
			$endmonth = $arr["request"]["det_mon"]+1;
		}
		$req_start = mktime(0,0,0, $arr["request"]["det_mon"], $startday, $arr["request"]["det_year"]);
		$req_end = mktime(23,59,59, $endmonth, $endday, $arr["request"]["det_year"]);

		$startd = mktime(0,0,0, $arr["request"]["det_mon"], $startday, $arr["request"]["det_year"]);
		$endd = mktime(23,59,59, $arr["request"]["det_mon"]+1, $endday, $arr["request"]["det_year"]);

		$ui = get_instance(CL_USER);
		$pid = $ui->get_person_for_uid($arr["request"]["det_uid"])->id();

		if($arr["request"]["stat_proj_bugs"] || !$arr["request"]["stat_proj_hrs_end"])
		{
			$ol = new object_list(array(
				"class_id" => CL_BUG_COMMENT,
				"lang_id" => array(),
				"site_id" => array(),
				"created" => new obj_predicate_compare(
					OBJ_COMP_BETWEEN_INCLUDING,
					$req_start,
					$req_end
				),
				"createdby" => $arr["request"]["det_uid"]
			));
			foreach($ol->arr() as $com)
			{
				$bug = obj($com->parent());
				if ($bug->prop("project") == $arr["request"]["det_proj"])
				{
					$bugs[$com->parent()]["hrs"] += $com->prop("add_wh");
					if(!$bugs[$com->parent()]["lastdate"] || $bugs[$com->parent()]["lastdate"] > $com->created())
					$bugs[$com->parent()]["lastdate"] = $com->created();
				}
			}
		}/*
		$types = $this->get_event_types();
		foreach($types as $type)
		{
			if($arr["request"]["stat_proj_".$type["rname"]] || !$arr["request"]["stat_proj_hrs_end"])
			{
				$c = new connection();
				$list = $c->find(array(
					"to.class_id" => $type["class_id"],
					"from.class_id" => CL_CRM_PERSON,
					"type" => $type["types"],
					"from" => $pid,
				));
				foreach($list as $item)
				{
					$o = obj($item["to"]);
					$conn = $o->connections_from(array(
						"type" => "RELTYPE_PROJECT",
					));
					$is_proj = false;
					foreach($conn as $c)
					{
						if($c->prop("to") == $arr["request"]["det_proj"])
						{
							$is_proj = true;
						}
					}
					if($o->prop("is_work") && $o->prop("start1") > $startd && $o->prop("start1") < $endd && $is_proj)
					{
						$bugs[$item["to"]]["hrs"] += $o->prop($type["timevar"]);
						$bugs[$item["to"]]["lastdate"] = ($o->class_id() == CL_BUG) ? $o->modified() : $o->prop("start1");
					}
				}
			}
		}*/
		$classes = array();
		if($arr["request"]["stat_hr_tasks"]) $classes[] = CL_TASK;
		if($arr["request"]["stat_hr_calls"]) $classes[] = CL_CRM_CALL;
		if($arr["request"]["stat_hr_meetings"]) $classes[] = CL_CRM_MEETING;
		if($arr["request"]["stat_hr_bugs"]) $classes[] = CL_BUG;

		$ol = new object_list(array(
			"class_id" => CL_TASK_ROW,
			"impl" => $pid,
			"site_id" => array(),
			"lang_id" => array(),
			"date" => new obj_predicate_compare(
				OBJ_COMP_BETWEEN_INCLUDING,
				$req_start,
				$req_end
			),
			"task.class_id" => $classes,
		));
		foreach($ol->arr() as $oid => $o)
		{
			$conn = $o->connections_to(array(
				"type" => "RELTYPE_ROW",
				"from.class_id" => CL_TASK,
			));
			$c = reset($conn);
			$task_o = $c->from();
			$conn2 = $task_o->connections_from(array(
				"type" => "RELTYPE_PROJECT",
			));
			$is_proj = false;
			foreach($conn2 as $c)
			{
				if($c->prop("to") == $arr["request"]["det_proj"])
				{
					$is_proj = true;
				}
			}
			if($is_proj)
			{
				$bugs[$oid]["hrs"] += $o->prop("time_real");
				$bugs[$oid]["lastdate"] = $o->prop("date");
			}
		}
		$this->_insert_det_data_from_arr($bugs, &$t);

		$u = get_instance(CL_USER);
		$p = $u->get_person_for_uid($arr["request"]["det_uid"]);
		$proj = obj($arr["request"]["det_proj"]);

		$t->set_caption(sprintf(t("%s t&ouml;&ouml;tunnid projektis %s ajavahemikul %s - %s"),
			$p->name(),
			$proj->name(),
			date("d.m.Y", $req_start),
			date("d.m.Y", $req_end)
		));
	}

	function _get_stat_proj_ppl($arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->instance()->get_people_list($arr["obj_inst"]);
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_proj_gantt($arr)
	{
		$proj_list = $this->_proj_gantt_proj_list();

		$rv = $this->_get_my_bug_list_with_times($arr["obj_inst"]);
		foreach($rv as $item)
		{
			if ($this->can("view", $item["b"]->project) && !isset($proj_list[$item["b"]->project]))
			{
				$proj_list[$item["b"]->project] = obj($item["b"]->project);
			}
		}
		$this->_proj_gantt_draw($arr, $proj_list);
	}

	private function _get_display_person()
	{
		if ($this->can("view", $_GET["filt_p"]))
		{
			$p = obj($_GET["filt_p"]);
		}
		else
		{
			$u = get_instance(CL_USER);
			$p = obj($u->get_current_person());
		}
		return $p;
	}

	private function _proj_gantt_draw($arr, $project_list)
	{
		list($range_start, $range_end) = $this->_proj_gantt_get_limits($project_list);
		$chart = get_instance ("vcl/gantt_chart");

		$columns = 7;
		$gt_days_in_col = ceil(($range_end - $range_start) / 7) / (24*3600);

		$col_length = $gt_days_in_col*24*60*60;

		$p = $this->_get_display_person();

		$subdivisions = 1;

		foreach($project_list as $p)
		{
			$chart->add_row (array (
				"name" => $p->id(),
				"title" => parse_obj_name($p->name()),
				"uri" => html::get_change_url(
					$p->id(),
					array("return_url" => get_ru())
				),
			));
		}

		foreach($project_list as $p)
		{
			$title = parse_obj_name($p->name())."<br>( ".date("d.m.Y H:i", $p->start)." - ".date("d.m.Y H:i", $p->end)." ) ";
			$bar = array (
				"id" => $p->id (),
				"row" => $p->id (),
				"start" => $p->start,
				"length" => $p->end - $p->start,
				"title" => $title,
				"colour" => "#ff0000",
			);
			$chart->add_bar ($bar);
		}
		$chart->configure_chart (array (
			"chart_id" => "bt_gantt_proj",
			"style" => "aw",
			"start" => $range_start,
			"end" => $range_end,
			"columns" => $columns,
			"subdivisions" => $subdivisions,
			"timespans" => $subdivisions,
			"width" => 1000,
			"row_height" => 10,
			"column_length" => $col_length,
			"timespan_range" => $col_length,
		));

		### define columns
		for($i = 0; $i < $columns; $i++)
		{
			$t = $range_start + ($i * $col_length);
			$t2 = $range_start + (($i+1) * $col_length);
			$chart->define_column (array (
				"col" => ($i + 1),
				"title" => date("d.m.Y", $t)."<br>".date("d.m.Y", $t2),
				"uri" => "#",
			));
		}

		$arr["prop"]["value"] = $chart->draw_chart ();
	}

	private function _proj_gantt_proj_list()
	{
		$p = $this->_get_display_person();
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_PROJECT.RELTYPE_PARTICIPANT" => $p->id(),
			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
			"state" => 1
		));
		$rv = array();
		foreach($ol->arr() as $p)
		{
			$rv[$p->id] = $p;
		}
		return $rv;
	}

	private function _proj_gantt_get_limits($project_list)
	{
		$min = null;
		$max = null;
		foreach($project_list as $p)
		{
			if ($p->start > 100 && ($min === null || $p->start < $min))
			{
				$min = $p->start;
			}
			if ($p->end > 100 && ($max === null || $p->end > $max))
			{
				$max = $p->end;
			}
		}
		return array(max($min, time()), $max);
	}

	function _get_proj_bug_gantt($arr)
	{
		$proj_list = $this->_proj_gantt_proj_list();
		$this->_proj_bug_gantt_draw($arr, $proj_list);
	}

	private function _proj_bug_gantt_draw($arr, $project_list)
	{
		$rv = $this->_get_my_bug_list_with_times($arr["obj_inst"]);
		foreach($rv as $item)
		{
			if ($this->can("view", $item["b"]->project) && !isset($project_list[$item["b"]->project]))
			{
				$project_list[$item["b"]->project] = obj($item["b"]->project);
			}
		}
		list($range_start, $range_end) = $this->_proj_gantt_get_limits($project_list);
		$range_end = min($range_end, time() + 3 * 30 * 24 * 3600);

		$chart = get_instance ("vcl/gantt_chart");

		$columns = 7;
		$gt_days_in_col = ceil(($range_end - $range_start) / 7) / (24*3600);

		$col_length = $gt_days_in_col*24*60*60;

		$p = $this->_get_display_person();

		$subdivisions = 1;

		foreach($project_list as $p)
		{
			$chart->add_row (array (
				"name" => $p->id(),
				"title" => parse_obj_name($p->name()),
				"uri" => html::get_change_url(
					$p->id(),
					array("return_url" => get_ru())
				),
			));
		}

		// list all open bugs for me for all the given projects
		$proj2bug = $this->_get_proj2bug_list($project_list);

		$onepixeltime = ($range_end - $range_start) / 1000;

		foreach($project_list as $p)
		{
			$title = parse_obj_name($p->name())."<br>( ".date("d.m.Y H:i", $p->start)." - ".date("d.m.Y H:i", $p->end)." ) ";
			$bar = array (
				"id" => $p->id (),
				"row" => $p->id (),
				"start" => $p->start,
				"length" => $p->end - $p->start,
				"title" => $title,
				"colour" => "#ff0000",
			);
			$chart->add_bar ($bar);

			foreach(safe_array($proj2bug[$p->id]) as $b)
			{
				$time_data = $rv[$b->id];
				$bar = array (
					"id" => $b->id (),
					"row" => $p->id (),
					"start" => $time_data["start"],
					"length" => max($onepixeltime*2, $time_data["end"] - $time_data["start"]),
					"title" => parse_obj_name($b->name()),
					"colour" => "#00ff00",
				);
				$chart->add_bar ($bar);
			}
		}
		$chart->configure_chart (array (
			"chart_id" => "bt_gantt_proj",
			"style" => "aw",
			"start" => $range_start,
			"end" => $range_end,
			"columns" => $columns,
			"subdivisions" => $subdivisions,
			"timespans" => $subdivisions,
			"width" => 1000,
			"row_height" => 10,
			"column_length" => $col_length,
			"timespan_range" => $col_length,
		));

		### define columns
		for($i = 0; $i < $columns; $i++)
		{
			$t = $range_start + ($i * $col_length);
			$t2 = $range_start + (($i+1) * $col_length);
			$chart->define_column (array (
				"col" => ($i + 1),
				"title" => date("d.m.Y", $t)."<br>".date("d.m.Y", $t2),
				"uri" => "#",
			));
		}

		$arr["prop"]["value"] = $chart->draw_chart ();
	}

	private function _get_proj2bug_list($p_list)
	{
		$p_ids = array();
		foreach($project_list as $p)
		{
			$p_ids[$p->id] = $p->id;
		}
		
		$bug_list = new object_list(array(
			"class_id" => CL_BUG,
			"bug_status" => array(BUG_OPEN,BUG_INPROGRESS,BUG_FATALERROR,BUG_TESTING,BUG_VIEWING),
			"CL_BUG.who.name" => $this->_get_display_person()->name(),
			"lang_id" => array(),
			"site_id" => array(),
			"project" => $p_ids
		));
		$rv = array();
		foreach($bug_list->arr() as $b)
		{
			$rv[$b->project][] = $b;
		}
		return $rv;
	}

	private function _get_my_bug_list_with_times($obj_inst)
	{
		classload("core/date/date_calc");
		$rv = array();

		$p = $this->_get_display_person();

		$i = get_instance(CL_BUG_TRACKER);
		if (is_object($obj_inst))
		{
			$i->combined_priority_formula = $obj_inst->prop("combined_priority_formula"); // required by get_undone_bugs_by_p(), __gantt_sort()
		}


		$range_start = get_day_start();

		$gt_list = $i->get_undone_bugs_by_p($p);
		$bi = get_instance(CL_BUG);

		$i->day2wh = $i->get_person_whs($p);

		$i->gt_start = $i->get_next_avail_time_from(time(), $i->day2wh);
		$i->gt_days_in_col = 1;
		
		$sect = $i->get_sect();
		$curday = 0;
		$i->job_count = count($gt_list);
		foreach ($gt_list as $gt)
		{
			$i->gt_start = $i->get_next_avail_time_from($i->gt_start, $i->day2wh);
			if ($gt->prop("num_hrs_guess") > 0)
			{
				$length = $gt->prop("num_hrs_guess") * 3600 - ($gt->prop("num_hrs_real") * 3600);
				if ($length < 0)
				{
					$length = 3600;
				}
			}
			else
			{
				$length = 7200;
			}
			$i->job_hrs += $length;
			$i->check_sect($sect, $curday);
			$cdata = $i->get_gantt_bug_colors($gt);
			$color = $cdata["color"];
			if ($length > $sect[$curday]["len"])
			{
				// split into parts
				$tot_len = $length;
				$length = $sect[$curday]["len"];
				$remaining_len = $tot_len - $length;
				$title = parse_obj_name($gt->name())."<br>( ".date("d.m.Y H:i", $i->gt_start)." - ".date("d.m.Y H:i", $i->gt_start + $length)." ) ";
				$this->_add_rv($rv, $gt, $i->gt_start, $length);
				$i->gt_start += $length;
				$curday++;

				while($remaining_len > 0)
				{
					$i->check_sect($sect, $curday);
					$length = min($remaining_len, $sect[$curday]["len"]);
					$remaining_len -= $length;
					$i->gt_start = $i->get_next_avail_time_from($i->gt_start, $i->day2wh);

					$this->_add_rv($rv, $gt, $i->gt_start, $length);

					$i->gt_start += $length;
					$sect[$curday]["len"] -= $length;
				}
			}
			else
			{
				$sect[$curday]["len"] -= $length;
				$this->_add_rv($rv, $gt, $i->gt_start, $length);

				$i->gt_start += $length;
			}
		}
		return $rv;
	}

	private function _add_rv(&$rv, $gt, $start, $len)
	{
		if (!isset($rv[$gt->id]))
		{
			$rv[$gt->id] = array(
				"start" => $start,
				"end" => $start + $len,
				"b" => $gt
			);
		}
		else
		{
			$rv[$gt->id]["end"] = $start + $len;
		}
	}
}

