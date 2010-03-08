<?php
/*
@classinfo syslog_type=ST_CRM_PERSON_WH_TABLE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_crm_person_wh_table master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_person_wh_table
@default group=general

	@property owner type=relpicker reltype=RELTYPE_OWNER field=aw_owner
	@caption Organisatsioon

	@property ppl type=chooser reltype=RELTYPE_PERSON multiple=1 store=connect editonly=1 orient=vertical
	@caption Isikud

@default group=modify_hours

	@property wanted_hours_tb type=toolbar store=no no_caption=1
	@property wanted_hours_table type=table store=no no_caption=1

@default group=current_hours

	@property current_hours_table type=table store=no no_caption=1

@default group=hour_entries

	@property hour_entry_tb type=toolbar no_caption=1 store=no

	@layout hour_entry_split type=hbox width=20%:80%

		@layout hour_entry_left type=vbox parent=hour_entry_split

			@layout hour_entry_tree type=vbox closeable=1 area_caption=Aja&nbsp;filter  parent=hour_entry_left

				@property hour_entry_tree type=treeview store=no no_caption=1 parent=hour_entry_tree


		@property hour_entry_table type=table store=no no_caption=1 parent=hour_entry_split



@groupinfo wanted_hours caption="Kohustuslikud tunnid" submit=no

	@groupinfo current_hours caption="Kehtivad tunnid" parent=wanted_hours submit=no
	@groupinfo modify_hours caption="Muuda kehtivaid tunde" parent=wanted_hours submit=no

@groupinfo hour_entries caption="Reaalsed tunnid" submit=no



@reltype OWNER value=1 clid=CL_CRM_COMPANY
@caption Omanik

@reltype PERSON value=2 clid=CL_CRM_PERSON
@caption Isik
*/

class crm_person_wh_table extends class_base
{
	function crm_person_wh_table()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_person_wh_table",
			"clid" => CL_CRM_PERSON_WH_TABLE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_person_wh_table(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_owner":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_ppl($arr)
	{	
		if (!$this->can("view", $arr["obj_inst"]->owner))
		{
			return PROP_IGNORE;
		}
		$arr["prop"]["options"] = obj($arr["obj_inst"]->owner)->get_workers()->names();
	}

	private function _init_wanted_hours_table($t)
	{
		$t->define_field(array(
			"name" => "dates",
			"caption" => t("Kehtib"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "date_from",
			"caption" => t("Kehtib alates"),
			"align" => "center",
			"sortable" => 1,
//			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y",
			"parent" => "dates"
		));
		$t->define_field(array(
			"name" => "date_to",
			"caption" => t("Kehtib kuni"),
			"align" => "center",
			"sortable" => 1,
//			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y",
			"parent" => "dates"
		));

		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tunnid"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "hours_total",
			"caption" => t("Kokku"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
			"parent" => "hours"
		));

		$t->define_field(array(
			"name" => "hours_cust",
			"caption" => t("Muutuvad"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
			"parent" => "hours"
		));

		$t->define_field(array(
			"name" => "hours_other",
			"caption" => t("P&uuml;sivad"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1,
			"parent" => "hours"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("&nbsp;"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_wanted_hours_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_wanted_hours_table($t);

		$ppl = $arr["obj_inst"]->get_people_list();
		foreach($ppl->arr() as $person)
		{
			$wh_list = $arr["obj_inst"]->get_must_wh_list_for_person($person);
			foreach($wh_list->arr() as $entry)
			{
				$t->define_data(array(
					"oid" => $entry->id,
					"person" => html::obj_change_url($person),
					"date_from" => date("d.m.Y", $entry->from),
					"date_to" => date("d.m.Y", $entry->to),
					"sort_date_from" => $entry->from,
					"sort_date_to" => $entry->to,
					"hours_total" => $entry->hours_total,
					"hours_cust" => $entry->hours_cust,
					"hours_other" => $entry->hours_other,
					"change" => html::get_change_url($entry->id(), array("return_url" => get_ru()), t("Muuda"))
				));
			}

			$t->define_data(array(
				"oid" => null,
				"person" => html::obj_change_url($person),
				"date_from" => html::date_select(array("name" => "t[".$person->id()."][-1][from]", "month" => "text", "day" => "text", "year" => "text")),
				"date_to" => html::date_select(array("name" => "t[".$person->id()."][-1][to]", "month" => "text", "day" => "text", "year" => "text")),
				"hours_total" => html::textbox(array("name" => "t[".$person->id()."][-1][total]", "size" => 5)),
				"hours_cust" => html::textbox(array("name" => "t[".$person->id()."][-1][cust]", "size" => 5)),
				"hours_other" => html::textbox(array("name" => "t[".$person->id()."][-1][other]", "size" => 5)),
				"sort_date_from" => 3
			));
		}

		$t->set_default_sortby("sort_date_from");
		$t->set_rgroupby(array("person" => "person"));
		$t->set_caption(t("Isikute kohustuslikud t&ouml;&ouml;tunnid"));
	}

	function _set_wanted_hours_table($arr)
	{
		$t = $arr["request"]["t"];
		$ppl = $arr["obj_inst"]->get_people_list();
		foreach($ppl->arr() as $person)
		{
			$pd = $t[$person->id()];
			if (is_array($pd))
			{
				foreach($pd as $row)
				{
					if ($row["total"] > 0)
					{
						$arr["obj_inst"]->add_must_wh_entry_for_person($person, $row);
					}
				}
			}
		}
	}

	function _get_wanted_hours_tb($arr)
	{	
		$arr["prop"]["vcl_inst"]->add_delete_button();
		$arr["prop"]["vcl_inst"]->add_save_button();
	}

	private function _init_current_hours_table($t)
	{
		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "right",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "hours_cust",
			"caption" => t("Muutuvtunnid"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "hours_other",
			"caption" => t("P&uuml;&uuml;situnnid"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "hours_total",
			"caption" => t("Kokku tunnid"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	function _get_current_hours_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_current_hours_table($t);

		$overview = $arr["obj_inst"]->get_current_required_hours();
		$sums = array("hours_total" => 0, "hours_cust" => 0, "hours_other" => 0);
		foreach($overview as $entry)
		{
			$t->define_data(array(
				"person" => obj($entry->person)->name,
				"hours_total" => $entry->hours_total,
				"hours_cust" => $entry->hours_cust,
				"hours_other" => $entry->hours_other,
			));
			$sums["hours_total"] += $entry->hours_total;
			$sums["hours_cust"] += $entry->hours_cust;
			$sums["hours_other"] += $entry->hours_other;
		}
		$t->set_default_sortby("person");
		$t->set_caption(t("Isikute kohustuslikud t&ouml;&ouml;tunnid"));
		$t->sort_by();
		$t->set_sortable(false);
		
		$t->define_data(array(
			"person" => html::strong(t("Summa")),
			"hours_total" => html::strong($sums["hours_total"]),
			"hours_cust" => html::strong($sums["hours_cust"]),
			"hours_other" => html::strong($sums["hours_other"]),
		));
	}

	function _get_hour_entry_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON_WH_TABLE_ENTRY,
			"lang_id" => array(),
			"site_id" => array(),
			"wh_table" => $arr["obj_inst"]->id()
		));
		$dates = array();
		foreach($ol->arr() as $o)
		{
			$dates[$o->year][$o->month] = 1;
		}

		$tv = $arr["prop"]["vcl_inst"];

		foreach($dates as $year => $months)
		{
			$tv->add_item(0, array(
				"id" => "year_".$year,
				"name" => $year,
				"url" => aw_url_change_var("year", $year, aw_url_change_var("month", null))
			));
			foreach($months as $month => $one)
			{
				$tv->add_item("year_".$year, array(
					"id" => "mon_".$month,
					"name" => $month,
					"url" => aw_url_change_var("month", $month, aw_url_change_var("year",$year))
				));
			}
		}
	}

	function _get_hour_entry_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_CRM_PERSON_WH_TABLE_ENTRY), $arr["obj_inst"]->id(), null, array("workspace" => $arr["obj_inst"]->id()));
		$tb->add_delete_button();
	}

	private function _init_hour_entry_table($t)
	{
		$t->set_caption(t("T&ouml;&ouml;tundide sisestused"));
		$t->define_field(array(
			"name" => "year",
			"caption" => t("Aasta"),
			"sortable" => 1,
			"align" => "right",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "month",
			"caption" => t("Kuu"),
			"sortable" => 1,
			"align" => "right",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
	}

	function _get_hour_entry_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_hour_entry_table($t);

		$filt = array(
			"class_id" => CL_CRM_PERSON_WH_TABLE_ENTRY,
			"lang_id" => array(),
			"site_id" => array(),
			"wh_table" => $arr["obj_inst"]->id()
		);

		if (!empty($arr["request"]["year"]))
		{
			$filt["year"] = $arr["request"]["year"];
		}		
		if (!empty($arr["request"]["month"]))
		{
			$filt["month"] = $arr["request"]["month"];
		}		

		if (empty($filt["month"]) && empty($filt["year"]))
		{
			$filt["limit"] = 10;
			$filt[] = new obj_predicate_sort(array("year" => "desc", "month" => "desc"));
		}

		$ol = new object_list($filt);
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"year" => $o->year,
				"month" => $o->month,
				"change" => html::obj_change_url($o),
				"oid" => $o->id()
			));
		}
	}
}

?>
