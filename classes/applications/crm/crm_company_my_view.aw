<?php

namespace automatweb;

class crm_company_my_view extends class_base
{
	function crm_company_my_view()
	{
		$this->init("crm");
	}

	function _get_my_view($arr)
	{
		$this->read_template("my_day.tpl");
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$cur_co = get_current_company();
		$curp = get_current_person();
		$co = get_instance(CL_CRM_COMPANY);
		$this->vars(array(
			"uid" => aw_global_get("uid"),
			"date" => date("d.m.Y / H:i"),
			"date2" => date("d.m.Y"),
			"date3" => date("d.m.Y", time() + 24*3600),
			"add_event" => $this->mk_my_orb('new',array(
				'alias_to_org' => $cur_co->id() == $arr['obj_inst']->id() ? null : $arr['obj_inst']->id(),
				'reltype_org' => 13,
				'add_to_cal' => $this->cal_id,
				'clid' => CL_TASK,
				'title' => t("Toimetus"),
				'parent' => $arr["obj_inst"]->id(),
				'return_url' => get_ru()
			), CL_TASK),
			"add_link" => html::get_new_url(CL_EXTLINK, $curp->id(), array("return_url" => get_ru())),
			"change_pwd" => $this->mk_my_orb("change_pwd", array(), "users"),
			"logout" => $this->mk_my_orb("logout", array(), "users"),
			"POLL" => $this->_do_poll($arr),
			"others" => $this->picker($curp->id(), $co->get_employee_picker(null, true, true)),
			"EVENT" => $this->_events($arr, time()),
			"EVENT2" => $this->_events($arr, time() + 24*3600),
			"MY_FILE" => $this->_my_files($arr),
			"MY_LINK" => $this->_my_links($arr),
			"forum" => $this->_forums($arr)
		));

		return $this->parse();
		$this->read_template("my_view.tpl");

		/*
			Teha Avalehe vaade, kus on näha tänased ja homsed sündmused,
			mulle lisatud failid, foorumi viimased teemad,
		*/

		classload("vcl/table");
		classload("core/date/date_calc");
		classload("core/icons");

		$this->vars(array(
			"events" => $this->_events($arr),
			"files" => $this->_files($arr),
			"forums" => $this->_forums($arr)
		));

		return $this->parse();
	}

	function _init_events_t(&$t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
			"align" => "center",
			"width" => 1,
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "when",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => 1,
			"callb_pass_row" => 1,
			"callback" => array(&$this, "_format_when")
		));

		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "proj",
			"caption" => t("Projekt"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "parts",
			"caption" => t("Osalejad"),
			"align" => "center",
			"sortable" => 1
		));
	}

	function _format_when($row)
	{
		return date("H:i", $row["from"]).($row["to"] > 100 ? " - ".date("H:i", $row["to"]) : "");
	}

	function _files($arr)
	{
		$u = new user();
		$co = obj($u->get_current_company());

		$t = new vcl_table();

		$p = array(
			"obj_inst" => $co,
			"request" => array(
				"group" => "ovrv_offers"
			),
			"prop" => array(
				"vcl_inst" => &$t
			)
		);

		$i = get_instance("applications/crm/crm_company_overview_impl");
		$i->_get_my_tasks($p);

		return $t->draw();
	}

	function _forums($arr)
	{
		classload("vcl/table");
		// get forum from co and last topics from that
		$u = new user();
		$co = obj($u->get_current_company());

		$fo = $co->get_first_obj_by_reltype("RELTYPE_FORUM");
		if (!$fo)
		{
			return;
		}

		$f = $fo->instance();


		$folders = new object_tree(array(
			"class_id" => CL_MENU,
			"status" => STAT_ACTIVE,
			"parent" => $fo->prop("topic_folder")
		));

		list($t_counts, $t_list) = $f->get_topic_list(array("parents" => $folders->ids() + array($fo->prop("topic_folder"))));

		$pts = array();
		foreach($t_list as  $pt => $topics)
		{
			foreach($topics as $topic)
			{
				$pts[] = $topic;
			}
		}

		list($comm_c, $tot) = $f->get_comment_counts(array("parents" => $pts));

		$t = new vcl_table();
		$this->_init_topic_t($t);
		$u = new user();
		foreach($t_list as  $pt => $topics)
		{
			foreach($topics as $topic)
			{
				$l_c = $f->get_last_comments(array("parents" => array($topic)));

				$to = obj($topic);
				$url = $this->mk_my_orb("change", array(
					"id" => $topic,
					"group" => "contents",
					"topic" => $topic,
					"return_url" => get_ru()
				), CL_FORUM_V2);
				$uo = obj($u->get_person_for_uid($to->createdby()));
				$t->define_data(array(
					"name" => html::href(array(
						"url" => $url,
						"caption" => $to->name()
					)),
					"num" => $comm_c[$topic],
					"last" => $l_c["created"],
					"icon" => icons::get_icon($to),
					"author" => $uo->name()
				));
			}
		}

		return $t->draw();
	}

	function _init_topic_t(&$t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
			"align" => "center",
			"width" => 1
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Teema"),
			"align" => "left"
		));

		$t->define_field(array(
			"name" => "num",
			"caption" => t("Vastuseid"),
			"width" => 20,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "author",
			"caption" => t("Autor"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "last",
			"caption" => t("Viimane kommentaar"),
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
	}

	function _get_polls_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa kiirk&uuml;sitlus"),
			"url" => html::get_new_url(CL_POLL, $arr["obj_inst"]->id(), array("return_url" => get_ru()))
		));
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => "save_default_poll"
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud pollid"),
			"action" => "submit_delete_docs"
		));
	}

	function _init_polls_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "def",
			"caption" => t("Aktiivne"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"value" => "oid"
		));
	}

	function _get_polls_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_polls_t($t);

		$ol = new object_list(array(
			"class_id" => CL_POLL,
			"parent" => $arr["obj_inst"]->id(),
			"lang_id" => array(),
			"site_id" => array()
		));
		$def_poll = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_DEF_POLL");
		if ($def_poll)
		{
			$def_poll = $def_poll->id();
		}
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"def" => html::radiobutton(array(
					"name" => "def_poll",
					"value" => $o->id(),
					"checked" => $def_poll == $o->id()
				)),
				"oid" => $o->id()
			));
		}
	}

	function _do_poll($arr)
	{
		// draw def poll
		$def = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_DEF_POLL");
		if ($def)
		{
			aw_global_set("section", aw_ini_get("frontpage"));
			$poll = get_instance(CL_POLL);
			$this->vars(array(
				"poll_ct" => $arr["request"]["answer_id"] ? $poll->show($def->id()) : $poll->gen_user_html($def->id())
			));
			return $this->parse("POLL");
		}
	}

	function _events($arr, $tm)
	{
		$p = get_current_person();
		classload("core/icons");
		$i = get_instance("applications/crm/crm_company_overview_impl");
		$filt = $i->_get_tasks_search_filt(
			array(
				"act_s_part" => $p->name(),
				"act_s_status" => 1,
				"act_s_dl_from" => array(
					"year" => date("Y", $tm),
					"month" => date("m", $tm),
					"day" => date("d", $tm)
				),
				"act_s_dl_to" => array(
					"year" => date("Y", $tm),
					"month" => date("m", $tm),
					"day" => date("d", $tm)+1
				)
			),
			array(),
			array(CL_TASK,CL_CRM_MEETING,CL_CRM_CALL)
		);
		$ol = new object_list($filt);
		$ev = "";
		foreach($ol->arr() as $o)
		{
			$parts = array();
			foreach($o->connections_to(array("from.class_id" => CL_CRM_PERSON)) as $c)
			{
				if ($c->prop("from") != $p->id())
				{
					$parts[] = $c->prop("from");
				}
			}
			$this->vars(array(
				"icon" => icons::get_icon_url($o->class_id()),
				"timespan" => $this->_format_when(array("from" => $o->prop("start1"), "to" => $o->prop("end"))),
				"name" => html::obj_change_url($o),
				"cust" => html::obj_change_url($o->prop("customer")),
				"parts" => html::obj_change_url($parts)
			));
			$ev .= $this->parse("EVENT");
		}
		return $ev;
	}

	function _my_files($arr)
	{
		$rv = "";
		$clid = CL_CRM_DOCUMENT_ACTION;
		// now, find all thingies that I am part of
		$u = new user();
		$filt = array(
			"class_id" => CL_CRM_DOCUMENT_ACTION,
			"site_id" => array(),
			"lang_id" => array(),
			"actor" => $u->get_current_person(),
		);
		$filt["is_done"] = new obj_predicate_not(1);
		$ol = new object_list($filt);
		foreach($ol->arr() as $act)
		{
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

			$docs = array();
			$fi = new file();
			foreach($task->connections_from(array("type" => "RELTYPE_FILE")) as $c)
			{
				$fd = $fi->get_file_by_id($c->prop("to"), true);
				$docs[] = html::href(array(
					"caption" => $c->prop("to.name"),
					"url" => $fi->get_url($c->prop("to"), $c->prop("to.name"))
				))."  ".number_format(strlen($fd["content"]) / 1024, 1)."KB";
			}

			$this->vars(array(
				"icon" => icons::get_icon_url($task),
				"docs" => join(", ", $docs),
				"name" => html::obj_change_url($task),
				"size" => $sz
			));
			$rv .= $this->parse("MY_FILE");
		}
		return $rv;
	}

	function _my_links($arr)
	{
		$p = get_current_person();
		$rv = "";
		$ol = new object_list(array(
			"class_id" => CL_EXTLINK,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $p->id()
		));
		foreach($ol->arr() as $o)
		{
			$this->vars(array(
				"icon" => icons::get_icon_url($o),
				"url" => $o->prop("url"),
				"name" => $o->name(),
				"link" => html::get_change_url($o->id(), array("return_url" => get_ru()), $o->prop("comment")),
				"del_link" => $this->mk_my_orb("submit_delete_docs", array(
					"sel" => array($o->id()),
					"post_ru" => get_ru()
				), CL_CRM_COMPANY)
			));
			$rv .= $this->parse("MY_LINK");
		}
		return $rv;
	}
}

?>
