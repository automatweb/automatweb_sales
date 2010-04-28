<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/rostering_workbench.aw,v 1.10 2007/12/06 14:34:03 kristo Exp $
// rostering_workbench.aw - T&ouml;&ouml;aja planeerimine 
/*

@classinfo syslog_type=ST_ROSTERING_WORKBENCH relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_rostering master_table=objects master_index=brother_of index=aw_oid

@default table=objects
@default group=general

	@property owner type=relpicker reltype=RELTYPE_OWNER table=aw_rostering field=aw_owner
	@caption Omanik

@default group=ppl

	@property cedit_tb type=toolbar no_caption=1 store=no

	@layout contacts_edit type=hbox

		@layout contacts_edit_tree type=hbox parent=contacts_edit closeable=1 area_caption=Struktuur

			@property cedit_tree type=treeview store=no parent=contacts_edit_tree no_caption=1

		@layout contacts_edit_table type=hbox parent=contacts_edit 
			@property cedit_table type=table store=no parent=contacts_edit_table no_caption=1


@default group=cycles

	@property cycles_tb type=toolbar no_caption=1 store=no
	@property cycles_table type=table no_caption=1 store=no

@default group=scenarios

	@property sc_tb type=toolbar no_caption=1 store=no
	@property sc_table type=table no_caption=1 store=no

@default group=wa

	@property wa_tb type=toolbar no_caption=1 store=no
	@property wa_table type=table no_caption=1 store=no

@default group=shifts

	@property sh_tb type=toolbar no_caption=1 store=no
	@property sh_table type=table no_caption=1 store=no

@default group=skills

	@property sk_tb type=toolbar no_caption=1 store=no

	@layout sk_main type=hbox width=20%:80%

		@layout sk_tree type=hbox parent=sk_main closeable=1 area_caption=P&auml;devuste&nbsp;puu

			@property sk_tree type=treeview store=no no_caption=1 parent=sk_tree

		@property sk_table type=table no_caption=1 store=no parent=sk_main

@default group=stats_wp

	@property stats_wp type=table no_caption=1 store=no

@default group=stats_overtime

	@property stats_overtime type=table no_caption=1 store=no


@default group=skills_losing

	@layout skl_main type=hbox width=20%:80%

		@layout skl_tree type=hbox parent=skl_main closeable=1 area_caption=Organisatsiooni&nbsp;struktuur

			@property skl_tree type=treeview store=no no_caption=1 parent=skl_tree

		@property skl_table type=table no_caption=1 store=no parent=skl_main

@default group=graph

	@property g_tb type=toolbar no_caption=1 store=no

	@layout graph_split type=hbox width=20%:80%

		@layout graph_tree type=vbox parent=graph_split closeable=1 area_caption=Graafikud

			@property graph_tree type=treeview no_caption=1 store=no parent=graph_tree

		@property graph_tbl type=table no_caption=1 store=no parent=graph_split

@default group=work_hrs

	@property work_hrs_tb type=toolbar store=no no_caption=1
	@property work_hrs type=table store=no no_caption=1

@default group=payment_types

	@property payment_types_tb type=toolbar no_caption=1 store=no
	@property payment_types type=table store=no no_caption=1

@default group=other
	
	@property holidays type=textarea rows=10 cols=50 table=objects field=meta method=serialize
	@caption Riigip&uuml;had

@groupinfo ppl caption="Isikud" submit=no

@groupinfo settings caption="Seaded"
	@groupinfo cycles caption="Ts&uuml;klid" parent=settings submit=no
	@groupinfo scenarios caption="Stsenaariumid" parent=settings submit=no
	@groupinfo wa caption="T&ouml;&ouml;kohad" parent=settings submit=no
	@groupinfo shifts caption="Vahetused" parent=settings submit=no
	@groupinfo skills caption="P&auml;devused" parent=settings submit=no 
	@groupinfo skills_losing caption="P&auml;devuste kadumine" parent=settings submit=no 
	@groupinfo payment_types caption="Tasu liigid" parent=settings submit=no 
	@groupinfo other caption="Muud seaded" parent=settings 

@groupinfo stats caption="Statistika"
	@groupinfo stats_wp caption="T&ouml;&ouml;postid" parent=stats submit=no
	@groupinfo stats_overtime caption="&Uuml;letunnid" parent=stats submit=no

@groupinfo graph caption="Graafikud" submit=no
@groupinfo work_hrs caption="T&ouml;&ouml;aruanded" 

@reltype OWNER value=1 clid=CL_CRM_COMPANY
@caption Omanik

@reltype CYCLE value=2 clid=CL_PERSON_WORK_CYCLE
@caption Omanik

@reltype SCENARIO value=3 clid=CL_ROSTERING_SCENARIO
@caption Stsenaarium

@reltype WORKPLACE value=4 clid=CL_ROSTERING_WORKPLACE
@caption T&ouml;&ouml;koht

@reltype SHIFT value=5 clid=CL_ROSTERING_SHIFT
@caption Vahetus

@reltype SKILL value=6 clid=CL_PERSON_SKILL
@caption P&auml;devus

@reltype GRAPH_FOLDER value=7 clid=CL_MENU
@caption Graafikute kataloog

@reltype PAYMENT_TYPE value=8 clid=CL_ROSTERING_PAYMENT_TYPE
@caption Tasu liik

*/

class rostering_workbench extends class_base
{
	const AW_CLID = 1135;

	function rostering_workbench()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/rostering_workbench",
			"clid" => CL_ROSTERING_WORKBENCH
		));
		classload("core/date/date_calc");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "cedit_tb":
			case "cedit_tree":
			case "cedit_table":
				return $this->_fwd_co($arr);
		
			case "cycles_tb":
				$this->_cycles_tb($arr);
				break;

			case "cycles_table":
				$this->_cycles_table($arr);
				break;

			case "sc_tb":
				$this->_sc_tb($arr);
				break;

			case "sc_table":
				$this->_sc_table($arr);
				break;

			case "wa_tb":
				$this->_wa_tb($arr);
				break;

			case "wa_table":
				$this->_wa_table($arr);
				break;

			case "sh_tb":
				$this->_sh_tb($arr);
				break;

			case "sh_table":
				$this->_sh_table($arr);
				break;

			case "sk_tb":
				$this->_sk_tb($arr);
				break;

			case "sk_table":
				$this->_sk_table($arr);
				break;
		
			case "sk_tree":
				$this->_sk_tree($arr);
				break;
		
			case "stats_wp":
				$this->_stats_wp($arr);
				break;

			case "stats_overtime":
				$this->_stats_overtime($arr);
				break;

			case "skl_tree":
				$this->_skl_tree($arr);
				break;

			case "skl_table":
				$this->_skl_table($arr);
				break;

			case "graph_tree":
				$this->_graph_tree($arr);
				break;

			case "graph_tbl":
				$this->_graph_tbl($arr);
				break;

			case "g_tb":
				$this->_g_tb($arr);
				break;

			case "work_hrs":
				$this->_work_hrs($arr);
				break;

			case "work_hrs_tb":
				$this->_work_hrs_tb($arr);
				break;

			case "payment_types_tb":
				$this->_payment_types_tb($arr);
				break;

			case "payment_types":
				$this->_payment_types($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "cedit_tb":
			case "cedit_tree":
			case "cedit_table":
				return $this->_setp_fwd_co($arr);
		}
		return $retval;
	}	

	function _fwd_co($arr)
	{
		static $i;
		if (!$i)
		{
			$i = get_instance(CL_CRM_COMPANY);
		}
		$obj = obj($arr["obj_inst"]->prop("owner"));
		$a2 = $arr;
		unset($a2["obj_inst"]);
		$a2["obj_inst"] = $obj;
		$a2["request"]["id"] = $obj->id();
		return $i->get_property($a2);
	}

	function _setp_fwd_co($arr)
	{
		static $i;
		if (!$i)
		{
			$i = get_instance(CL_CRM_COMPANY);
		}
		$obj = obj($arr["obj_inst"]->prop("owner"));
		$a2 = $arr;
		unset($a2["obj_inst"]);
		$a2["obj_inst"] = $obj;
		$a2["request"]["id"] = $obj->id();
		return $i->set_property($a2);
	}

	function callback_mod_retval($arr)
	{
		if($arr['request']['unit'])
		{
			$arr['args']['unit'] = $arr['request']['unit'];
		}

		if($arr['request']['cat'])
		{
			$arr['args']['cat'] = $arr['request']['cat'];
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr['unit'] = $_GET["unit"];
		$arr['cat'] = $_GET["cat"];
		$arr["sbt_data"] = 0;
		$arr["sbt_data2"] = 0;
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_rostering (aw_oid int primary key, aw_owner int)");
			return true;
		}
	}

	/**
		@attrib name=submit_delete_relations
	**/
	function submit_delete_relations($arr)
	{
		return $this->_sbt_fwd_co("submit_delete_relations", $arr);
	}

	/**
		@attrib name=submit_delete_ppl
	**/
	function submit_delete_ppl($arr)
	{
		return $this->_sbt_fwd_co("submit_delete_ppl", $arr);
	}

	/**
		@attrib name=cut_p
	**/
	function cut_p($arr)
	{
		return $this->_sbt_fwd_co("cut_p", $arr);
	}

	/**
		@attrib name=copy_p
	**/
	function copy_p($arr)
	{
		return $this->_sbt_fwd_co("copy_p", $arr);
	}

	/**
		@attrib name=paste_p
	**/
	function paste_p($arr)
	{
		return $this->_sbt_fwd_co("paste_p", $arr);
	}

	/**
		@attrib name=mark_p_as_important
	**/
	function mark_p_as_important($arr)
	{
		return $this->_sbt_fwd_co("mark_p_as_important", $arr);
	}

	function _sbt_fwd_co($act, $arr)
	{
		$i = get_instance(CL_CRM_COMPANY);
		$o = obj($arr["id"]);
		$arr["id"] = $o->prop("owner");
		return $i->$act($arr);
	}

	function _cycles_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_PERSON_WORK_CYCLE, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(), 
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 2
			)),
			"tooltip" => t("Lisa")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_cycles",
			"tooltip" => t("Kustuta ts&uuml;klid")
		));
	}

	function _init_cycles_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Ts&uuml;kkel"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "pri",
			"caption" => t("Prioriteet"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _cycles_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_cycles_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CYCLE")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"oid" => $o->id(),
				"pri" => $o->prop("ord")
			));
		}
	}

	function _sc_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_ROSTERING_SCENARIO, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(), 
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 3
			)),
			"tooltip" => t("Lisa")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_cycles",
			"tooltip" => t("Kustuta stsenaarium")
		));
	}

	function _init_sc_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Stsenaarium"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "cycles",
			"caption" => t("Ts&uuml;klid"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "work_hrs_per_week",
			"caption" => t("T&ouml;&ouml;tunde n&auml;dalas"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "no_plan_night",
			"caption" => t("&Auml;ra planeeri &ouml;&ouml;seks"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "max_overtime",
			"caption" => t("Maksimaalne &uuml;letundide arv"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "free_days_after_night_shift",
			"caption" => t("Vabu p&auml;evi peale &ouml;&ouml;t&ouml;&ouml;d"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _sc_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sc_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SCENARIO")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"oid" => $o->id(),
				"cycles" => $o->prop_str("cycles"),
				"work_hrs_per_week" => $o->prop("work_hrs_per_week"),
				"no_plan_night" => $o->prop("no_plan_night") ? t("X") : "",
				"max_overtime" => $o->prop("max_overtime"),
				"free_days_after_night_shift" => $o->prop("free_days_after_night_shift")
			));
		}
	}

	function _wa_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_ROSTERING_WORKPLACE, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(), 
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 4
			)),
			"tooltip" => t("Lisa")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_cycles",
			"tooltip" => t("Kustuta t&ouml;&ouml;kohad")
		));
	}

	function _init_wa_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("T&ouml;&ouml;koht"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "pdv",
			"caption" => t("P&auml;devused"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "adr",
			"caption" => t("Aadress"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "num_empl",
			"caption" => t("Mitu t&ouml;&ouml;tajat"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _wa_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_wa_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_WORKPLACE")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"oid" => $o->id(),
				"pdv" => html::obj_change_url($o->prop("skills")),
				"adr" => html::obj_change_url($o->prop("address")),
				"num_empl" => $o->prop("num_empl")
			));
		}
	}

	function _sh_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_ROSTERING_SHIFT, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(), 
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 5
			)),
			"tooltip" => t("Lisa")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_cycles",
			"tooltip" => t("Kustuta vahetused")
		));
	}

	function _init_sh_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Vahetus"),
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
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "start_time",
			"caption" => t("Algus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "end_time",
			"caption" => t("L&otilde;pp"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _sh_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sh_t($t);

		$i = get_instance(CL_ROSTERING_SHIFT);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SHIFT")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"oid" => $o->id(),
				"type" => $i->types[$o->prop("type")],
				"start_time" => $o->prop("start_time"),
				"end_time" => $o->prop("end_time"),
				"comment" => $o->comment()
			));
		}
	}

	/**
		@attrib name=delete_cycles
	**/
	function delete_cycles($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	function _sk_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$pt = $arr["request"]["skill_id"] ? $arr["request"]["skill_id"] : $arr["obj_inst"]->id();
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_PERSON_SKILL, $pt, array(
				"return_url" => get_ru(), 
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 6
			)),
			"tooltip" => t("Lisa")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_cycles",
			"tooltip" => t("Kustuta p&auml;devused")
		));
	}

	function _init_sk_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("P&auml;devus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _sk_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sk_t($t);
		$pt = $arr["request"]["skill_id"] ? $arr["request"]["skill_id"] : $arr["obj_inst"]->id();
		$ol = new object_list(array(
			"class_id" => CL_PERSON_SKILL,
			"parent" => $pt,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"oid" => $o->id()
			));
		}
	}

	function _init_stats_wp_t(&$t)
	{
		$t->define_field(array(
			"name" => "person",
			"caption" => t("&nbsp;"),
		));

		$ol = new object_list(array(
			"class_id" => CL_ROSTERING_WORKPLACE,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$t->define_field(array(
				"name" => $o->id(),
				"caption" => $o->name(),
				"align" => "center"
			));
		}
	}

	function _stats_wp($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stats_wp_t($t);

		classload("core/date/date_calc");
		$m = get_instance("applications/rostering/rostering_model");		
		$start = get_week_start();
		$end = get_week_start()+24*7*3600;

		// get schedulers for ppl
		$co = get_instance(CL_CRM_COMPANY);
		$empl = $co->get_employee_picker(obj($arr["obj_inst"]->prop("owner")));

		foreach($empl as $empl_id => $empl_name)
		{
			$work_times = $m->get_schedule_for_person(obj($empl_id), $start, $end);
			$d = array(
				"person" => html::obj_change_url($empl_id)
			);
			foreach($work_times as $wt_item)
			{
				$d[$wt_item["workplace"]] .= date("d.m.Y H:i", $wt_item["start"])." - ".date("d.m.Y H:i", $wt_item["end"])." <br>";
			}
			$t->define_data($d);
		}
	}

	function _init_stats_overtime_t(&$t)
	{
		$t->define_field(array(
			"name" => "section",
			"caption" => t("&Uuml;ksus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ot",
			"caption" => t("&Uuml;letunde"),
			"align" => "center"
		));
	}

	function _stats_overtime($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_stats_overtime_t($t);

		$co = get_instance(CL_CRM_COMPANY);
		$sects = $co->get_all_org_sections(obj($arr["obj_inst"]->prop("owner")));

		foreach($sects as $sect_id)
		{
			$t->define_data(array(
				"section" => html::obj_change_url($sect_id),
				"ot" => rand(1,50)
			));
		}
	}

	function _sk_tree($arr)
	{
		classload("vcl/treeview");
		classload("core/icons");
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "sk_tree",
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => CL_PERSON_SKILL,
				"parent" => $arr["obj_inst"]->id(),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "skill_id",
			"icon" => icons::get_icon_url(CL_PERSON_SKILL)
		));
	}

	function _skl_tree($arr)
	{
		$arr["prop"]["name"] = "unit_listing_tree";
		$this->_fwd_co($arr);
		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => "_crit",
			"parent" => 0,
			"name" => t("<font color=red>Kriitiline</font>"),
			"url" => aw_url_change_var("unit", "_crit")
		));
	}

	function _skl_table($arr)
	{
		$skill_list = new object_list(array(
			"class_id" => CL_PERSON_SKILL,
			"lang_id" => array(),
			"site_id" => array()
		));

		if ($arr["request"]["unit"] == "_crit")
		{
			$t =& $arr["prop"]["vcl_inst"];
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Nimi"),
				"align" => "center",
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "skill",
				"caption" => t("P&auml;devused")
			));
			$co = get_instance(CL_CRM_COMPANY);
			$ws = $co->get_employee_picker(obj($arr["obj_inst"]->prop("owner")));
			foreach($ws as $p_id => $p_n)
			{
				if (rand(1, 10) > 2)
				{
					continue;
				}
				$sks = "";
				foreach($skill_list->arr() as $skill)
				{
					$sks .= $skill->name().": kaob ".rand(1,40)." p&auml;eva p&auml;rast<br>";
				}
				$t->define_data(array(
					"name" => html::obj_change_url($p_id),
					"skill" => $sks
				));
			}
			return;
		}
		$arr["prop"]["name"] = "human_resources";
		$this->_fwd_co($arr);
		$t =& $arr["prop"]["vcl_inst"];
		$t->remove_field("phone");
		$t->remove_field("email");
		$t->remove_field("section");
		$t->remove_field("rank");

		$t->define_field(array(
			"name" => "skill",
			"caption" => t("P&auml;devused")
		));

		foreach($t->get_data() as $idx => $row)
		{
			$p = obj($row["id"]);
			$sks = "";
			foreach($skill_list->arr() as $skill)
			{
				$sks .= $skill->name().": kaob ".rand(1,40)." p&auml;eva p&auml;rast<br>";
			}
			$row["skill"] = $sks;
			$t->set_data($idx, $row);
		}		
	}



	function _graph_tree($arr)
	{
		classload("core/icons");
		$pt = $this->_get_graph_pt($arr["obj_inst"]);
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "graph_tree",
			),
			"root_item" => obj($pt),
			"ot" => new object_tree(array(
				"class_id" => CL_MENU,
				"parent" => $pt,
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "pt",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _init_graph_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Graafiku nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby_person",
			"caption" => t("Koostaja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "g_unit",
			"caption" => t("&Uuml;ksus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "g_start",
			"caption" => t("Algus"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y"
		));
		$t->define_field(array(
			"name" => "g_end",
			"caption" => t("L&otilde;pp"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y"
		));
		$t->define_field(array(
			"name" => "final",
			"caption" => t("Kinnitatud"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _graph_tbl($arr)
	{	
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_graph_t($t);

		$pt = $arr["request"]["pt"] ? $arr["request"]["pt"] : $this->_get_graph_pt($arr["obj_inst"]);
		$ol = new object_list(array(
			"class_id" => CL_ROSTERING_SCHEDULE,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $pt
		));
		$t->data_from_ol($ol, array("change_col" => "name"));
	}

	function _get_graph_pt($o)
	{
		$pt = $o->get_first_obj_by_reltype("RELTYPE_GRAPH_FOLDER");
		if (!$pt)
		{
			$f = obj();
			$f->set_class_id(CL_MENU);
			$f->set_name(sprintf(t("%s graafikud"), $o->name()));
			$f->set_parent($o->id());
			$f->save();
			$o->connect(array(
				"to" => $f->id(),
				"type" => "RELTYPE_GRAPH_FOLDER"
			));
			$pt = $f;
		}
		return $pt->id();
	}

	function _g_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$pt = $arr["request"]["pt"] ? $arr["request"]["pt"] : $this->_get_graph_pt($arr["obj_inst"]);
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa")
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Kaust"),
			"url" => html::get_new_url(CL_MENU, $pt, array("return_url" => get_ru(), "wp" => $arr["obj_inst"]->id()))
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Graafik"),
			"url" => html::get_new_url(CL_ROSTERING_SCHEDULE, $pt, array("return_url" => get_ru(), "wp" => $arr["obj_inst"]->id()))
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_cycles",
			"tooltip" => t("Kustuta graafikud")
		));
	}

	function _init_work_hrs_t(&$t)
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
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "graph",
			"caption" => t("Graafik"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "graph.g_start",
			"caption" => t("Algus"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "graph.g_end",
			"caption" => t("L&otilde;pp"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "graph.g_unit",
			"caption" => t("&Uuml;ksus"),
			"numeric" => 1,
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "graph.g_scenario",
			"caption" => t("Stsenaarium"),
			"numeric" => 1,
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _work_hrs($arr)
	{
		$t =&  $arr["prop"]["vcl_inst"];
		$this->_init_work_hrs_t($t);
		$ol = new object_list(array(
			"class_id" => CL_ROSTERING_WORK_ENTRY,
			"parent" => $arr["obj_inst"]->id()
		));
		$t->data_from_ol($ol, array("change_col" => "name"));
	}

	function _work_hrs_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_ROSTERING_WORK_ENTRY, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(), 
				"wp" => $arr["obj_inst"]->id()
			)),
			"tooltip" => t("Lisa")
		));
		// now add graphs submenu
		$pt = $this->_get_graph_pt($arr["obj_inst"]);
		$gfolders = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => $pt,
			"lang_id" => array(),
			"site_id" => array()
		));
		// list all graphs for the parents
		$ol = new object_list(array(
			"class_id" => CL_ROSTERING_SCHEDULE,
			"parent" => $gfolders->ids(),
			"lang_id" => array(),
			"site_id" => array()
		));
		$fld2graph = array();
		foreach($ol->arr() as $o)
		{
			$fld2graph[$o->parent()][] = $o;
		}
		foreach($gfolders->ids() as $gf_id)
		{
			if (true )
			{
				$fo = obj($gf_id);
				$tb->add_sub_menu(array(
					"parent" => $fo->parent() == $pt ? "new" : "p".$fo->parent(),
					"name" => "p".$gf_id,
					"text" => $fo->name()
				));
				if (is_array($fld2graph[$gf_id]))
				{
					foreach($fld2graph[$gf_id] as $gr)
					{
						$tb->add_menu_item(array(
							"parent" => "p".$gf_id,
							"text" => $gr->name(),
							"title" => $gr->name(),
							"link" => $this->mk_my_orb("create_we_from_graph", array("wb" => $arr["obj_inst"]->id(), "gr" => $gr->id(), "ru" => get_ru()))
						));
					}
				}
			}
		}
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_cycles",
			"tooltip" => t("Kustuta")
		));
	}

	function _payment_types($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_PAYMENT_TYPE"
			))), 
			array(
				"name", "createdby", "created", "hr_price"
			),
			CL_ROSTERING_PAYMENT_TYPE
		);
	}

	function _payment_types_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_ROSTERING_PAYMENT_TYPE), $arr["obj_inst"]->id(), 8);
		$tb->add_delete_button();
	}

	/**
		@attrib name=create_we_from_graph
		@param wb required 
		@param gr required
		@param ru required
	**/
	function create_we_from_graph($arr)
	{
		$gr = obj($arr["gr"]);

		$we = obj();
		$we->set_class_id(CL_ROSTERING_WORK_ENTRY);
		$we->set_parent($arr["wb"]);
		$we->set_name(sprintf(t("Graafiku %s t&ouml;&ouml;aruanne"), $gr->name()));
		$we->set_prop("graph", $gr->id());
		$we->set_prop("g_wp", $arr["wb"]);
		$we->save();
		return $this->mk_my_orb("change", array("id" => $we->id(), "return_url" => $arr["ru"]), $we->class_id());
	}
}
?>
