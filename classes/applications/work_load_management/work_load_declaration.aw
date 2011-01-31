<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_work_load_declaration master_index=brother_of master_table=objects index=aw_oid

@default table=aw_work_load_declaration

@default group=general
	
	@property manager type=objpicker clid=CL_WORK_LOAD_MANAGER field=aw_manager
	@caption T&ouml;&ouml;mahtude deklaratsiooni haldus

@default store=no

@groupinfo work_loads caption="T&ouml;&ouml;mahud"
@default group=work_loads

	@property wl_toolbar type=toolbar no_caption=1

	@layout wl_upper type=hbox width=50%:50%

		@layout wl_general type=vbox area_caption=T&ouml;&ouml;taja&nbsp;&uuml;ldandmed parent=wl_upper
			
			@property wl_name type=textbox parent=wl_general
			@caption Nimi
			
			@property wl_profession type=textbox parent=wl_general
			@caption Ametikoht
			
			@property wl_unit type=textbox parent=wl_general
			@caption Akadeemiline &uuml;ksus
			
			@property wl_salary type=textbox parent=wl_general
			@caption Lepinguj&auml;rgne p&otilde;hipalk

		@layout wl_summary type=vbox area_caption=T&ouml;&ouml;taja&nbsp;t&ouml;&ouml;mahtude&nbsp;kokkuv&otilde;te parent=wl_upper
			
			@property wl_teaching_load type=textbox disabled=1 parent=wl_summary
			@caption &Otilde;ppekoormus
			
			@property wl_research_load type=textbox disabled=1 parent=wl_summary
			@caption Teadust&ouml;&ouml; maht
			
			@property wl_total_load type=textbox disabled=1 parent=wl_summary
			@caption Akadeemilise t&ouml;&ouml; maht kokku
			
			@property wl_difference type=textbox disabled=1 parent=wl_summary
			@caption Proportsionaalne vahe n&otilde;uetega

	@layout wl_lower type=hbox width=25%:75%
	
		@layout wl_left type=vbox parent=wl_lower

			@property professions type=table no_caption=1 parent=wl_left

			@property competence type=table no_caption=1 parent=wl_left

			@property research_groups type=table no_caption=1 parent=wl_left
	
		@layout wl_right type=vbox parent=wl_lower

			@property rates type=table no_caption=1 parent=wl_right

@groupinfo academic_activity caption="Akadeemiline tegevus"
@default group=academic_activity

	@groupinfo contact_learning caption="Kontakt&otilde;pe" parent=academic_activity
	@default group=contact_learning

		@property cl_toolbar type=toolbar no_caption=1

		@property contact_learning_courses type=table no_caption=1

	@groupinfo e_learning caption="E-&otilde;pe" parent=academic_activity
	@default group=e_learning

		@property el_toolbar type=toolbar no_caption=1

		@property e_learning_courses type=table no_caption=1

	@groupinfo publications caption="Publikatsioonid" parent=academic_activity
	@default group=publications

		@property publications_toolbar type=toolbar no_caption=1

		@property publications type=table no_caption=1
	
	@groupinfo thesises caption="Kaitstud/oponeeritud tööd jm akadeemiline tegevus" parent=academic_activity
	@default group=thesises

		@property thesises_toolbar type=toolbar no_caption=1

		@property defended_thesises type=table no_caption=1

		@layout thesises_split type=hbox width=50%:50%

			@property opposed_thesises type=table no_caption=1 parent=thesises_split

			@property defended_phd_thesises type=table no_caption=1 parent=thesises_split

		@property other_academic_activities type=table no_caption=1

@groupinfo previous_period caption="Eelmine periood"
@default group=previous_period

	@property pp_toolbar type=toolbar no_caption=1

	@layout pp_split type=hbox width=50%:50%

		@property previous_declaration type=table no_caption=1 parent=pp_split

		@property premiums type=table no_caption=1 parent=pp_split

*/

class work_load_declaration extends class_base
{
	private $entry;

	public function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/work_load_management/work_load_declaration",
			"clid" => CL_WORK_LOAD_DECLARATION
		));
	}

	public function callback_pre_edit($arr)
	{
		$this->entry = $arr["obj_inst"]->get_declaration_entry_for_user();
	}

	public function _get_wl_name($arr)
	{
		$arr["prop"]["value"] = $this->entry->val("name");
	}

	public function _get_wl_profession($arr)
	{
		$arr["prop"]["value"] = $this->entry->val("profession");
	}

	public function _get_wl_unit($arr)
	{
		$arr["prop"]["value"] = $this->entry->val("unit");
	}

	public function _get_wl_salary($arr)
	{
		$arr["prop"]["value"] = $this->entry->val("salary");
	}

	public function _get_wl_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_save_button();

		return PROP_OK;
	}

	public function _get_el_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_save_button();

		return PROP_OK;
	}

	public function _get_cl_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_save_button();

		return PROP_OK;
	}

	public function _get_publications_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_save_button();

		return PROP_OK;
	}

	public function _get_pp_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_save_button();

		return PROP_OK;
	}

	protected function _professions_header($t)
	{
		$t->define_field(array(
			"name" => "active",
			"caption" => t(""),
			"callback" => array($this, "callback_professions_active"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "profession",
			"caption" => t("Ametikoht"),
		));
		$t->define_field(array(
			"name" => "load",
			"caption" => t("Koormus"),			
			"callback" => array($this, "callback_professions_load"),
			"callb_pass_row" => true,
		));
	}

	public function callback_professions_active($row)
	{
		return html::checkbox(array(
			"name" => "professions[{$row["id"]}][active]",
			"checked" => $row["active"],
			"onclick" => "if(this.checked){ $('#professions_{$row["id"]}__load_').removeAttr('disabled') } else { $('#professions_{$row["id"]}__load_').attr('disabled', 'disabled'); }"
		));
	}

	public function callback_professions_load($row)
	{
		return html::textbox(array(
			"name" => "professions[{$row["id"]}][load]",
			"value" => $row["load"],
			"size" => 4,
			"disabled" => empty($row["active"]),
		));
	}

	public function _get_professions($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_professions_header($t);

		$professions = $arr["obj_inst"]->manager()->get_professions()->names();
		$data = $this->entry->val("professions");

		foreach($professions as $id => $profession)
		{
			$t->define_data(array(
				"id" => $id,
				"profession" => $profession,
				"active" => !empty($data[$id]["active"]),
				"load" => !empty($data[$id]["load"]) ? $data[$id]["load"] : 0,
			));
		}

		return PROP_OK;
	}

	protected function _competence_header($t)
	{
		$t->define_field(array(
			"name" => "active",
			"caption" => t(""),
			"callback" => array($this, "callback_competence_active"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "competence",
			"caption" => t("Akadeemiline kompetents"),
		));
	}

	public function callback_competence_active($row)
	{
		return html::checkbox(array(
			"name" => "competences[{$row["id"]}][active]",
			"checked" => $row["active"],
		));
	}

	public function _get_competence($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_competence_header($t);

		$competences = $arr["obj_inst"]->manager()->get_competences()->names();
		$data = $this->entry->val("competences");

		foreach($competences as $id => $competence)
		{
			$t->define_data(array(
				"id" => $id,
				"competence" => $competence,
				"active" => !empty($data[$id]["active"])
			));
		}

		return PROP_OK;
	}

	protected function _research_groups_header($t)
	{
		$t->define_field(array(
			"name" => "active",
			"caption" => t(""),
			"callback" => array($this, "callback_research_groups_active"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "research_group",
			"caption" => t("Uurimisr&uuml;hm, grant"),
		));
	}

	public function callback_research_groups_active($row)
	{
		return html::checkbox(array(
			"name" => "research_groups[{$row["id"]}][active]",
			"checked" => $row["active"],
		));
	}

	public function _get_research_groups($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_research_groups_header($t);

		$research_groups = $arr["obj_inst"]->manager()->get_research_groups()->names();
		$data = $this->entry->val("research_groups");

		foreach($research_groups as $id => $research_group)
		{
			$t->define_data(array(
				"id" => $id,
				"research_group" => $research_group,
				"active" => !empty($data[$id]["active"]),
			));
		}

		return PROP_OK;
	}

	protected function _rates_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption("Kehtestatud arvestusm&auml;&auml;rad");

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Arvestusm&auml;&auml;r"),
		));

		
		$applicables = $arr["obj_inst"]->manager()->get_rate_applicables();

		foreach($applicables->names() as $oid => $name)
		{
			$t->define_field(array(
				"name" => "applicable_$oid",
				"caption" => $name
			));
		}
	}

	public function _get_rates($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_rates_header($arr);

		$rates = $arr["obj_inst"]->manager()->get_rates();

		foreach($rates->arr() as $oid => $rate)
		{
			$row = array(
				"oid" => $oid,
				"name" => $rate->name,
			);
			if(is_array($rate->applicables))
			{
				foreach($rate->applicables as $applicable_id => $applicable_value)
				{
					$row["applicable_".$applicable_id] = $applicable_value;
				}
			}
			$t->define_data($row);
		}

		return PROP_OK;
	}

	protected function _contact_learning_courses_header($t)
	{
		$t->set_caption(t("Kontakt&otilde;ppe kursused"));

		$t->define_field(array(
			"name" => "course",
			"caption" => t("Kursus"),
			"callback" => array($this, "callback_contact_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tunde"),
			"callback" => array($this, "callback_contact_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points",
			"caption" => t("AP"),
			"callback" => array($this, "callback_contact_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "participants",
			"caption" => t("Osalejaid"),
			"callback" => array($this, "callback_contact_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points_given",
			"caption" => t("Antavad AP"),
			"callback" => array($this, "callback_contact_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
	}

	public function callback_contact_learning_courses_textbox($row)
	{
		$args = array(
			"name" => "contact_learning_courses[{$row["id"]}][{$row["_this_cell"]}]",
			"value" => !empty($row[$row["_this_cell"]]) ? $row[$row["_this_cell"]] : 0,
			"size" => 4
		);

		switch($row["_this_cell"])
		{
			case "course":
				unset($args["size"]);
				$args["value"] = isset($row[$row["_this_cell"]]) ? $row[$row["_this_cell"]] : "";
				break;

			case "points_given":
				$args["disabled"] = true;
				break;
		}

		return html::textbox($args);
	}

	public function _get_contact_learning_courses($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_contact_learning_courses_header($t);
		$data = array_values($this->entry->val("contact_learning_courses"));

		for($i = 0; $i < count($data) + 4; $i++)
		{
			$row = array(
				"id" => $i
			);

			if(isset($data[$i]))
			{
				$row = array_merge($row, $data[$i]);
				if(empty($row["course"]))
				{
					continue;
				}
			}

			$t->define_data($row);
		}

		return PROP_OK;
	}

	protected function _e_learning_courses_header($t)
	{
		$t->set_caption(t("E-&otilde;ppe kursused"));

		$t->define_field(array(
			"name" => "course",
			"caption" => t("Kursus"),
			"callback" => array($this, "callback_e_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points",
			"caption" => t("AP"),
			"callback" => array($this, "callback_e_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "participants",
			"caption" => t("Osalejaid"),
			"callback" => array($this, "callback_e_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points_given",
			"caption" => t("Antavad AP"),
			"callback" => array($this, "callback_e_learning_courses_textbox"),
			"callb_pass_row" => true,
		));
	}

	public function callback_e_learning_courses_textbox($row)
	{
		$args = array(
			"name" => "e_learning_courses[{$row["id"]}][{$row["_this_cell"]}]",
			"value" => !empty($row[$row["_this_cell"]]) ? $row[$row["_this_cell"]] : 0,
			"size" => 4
		);

		switch($row["_this_cell"])
		{
			case "course":
				unset($args["size"]);
				$args["value"] = isset($row[$row["_this_cell"]]) ? $row[$row["_this_cell"]] : "";
				break;

			case "points_given":
				$args["disabled"] = true;
				break;
		}

		return html::textbox($args);
	}

	public function _get_e_learning_courses($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_e_learning_courses_header($t);
		$data = array_values($this->entry->val("e_learning_courses"));

		for($i = 0; $i < count($data) + 4; $i++)
		{
			$row = array(
				"id" => $i
			);

			if(isset($data[$i]))
			{
				$row = array_merge($row, $data[$i]);
				if(empty($row["course"]))
				{
					continue;
				}
			}

			$t->define_data($row);
		}

		return PROP_OK;
	}

	public function _defended_thesises_header($t)
	{
		$t->set_caption(t("Juhendamisel kaitstud tööd"));

		$t->define_field(array(
			"name" => "category",
			"caption" => t("Kaitstud töö kategooria")
		));
		for($i = 2004; $i < 2012; $i++)
		{
			$t->define_field(array(
				"name" => "acad_year_$i",
				"caption" => t(sprintf("%s-%s", strval($i), substr(strval($i+1), 2))),
				"callback" => array($this, "callback_defended_thesises_years"),
				"callb_pass_row" => true,
			));
		}
		$t->define_field(array(
			"name" => "total",
			"caption" => t("Kokku"),
			"callback" => array($this, "callback_defended_thesises_total"),
			"callb_pass_row" => true,
		));
	}

	public function callback_defended_thesises_years($row)
	{
		$year = substr($row["_this_cell"], -4);
		return html::textbox(array(
			"name" => "defended_thesises[{$row["id"]}][years][{$year}]",
			"value" => !empty($row["years"][$year]) ? $row["years"][$year] : 0,
			"size" => 4,
		));
	}

	public function callback_defended_thesises_total($row)
	{
		return html::textbox(array(
			"name" => "defended_thesises[{$row["id"]}][total]",
			"value" => array_sum($row["years"]),
			"size" => 4,
			"disabled" => true
		));
	}

	public function _get_defended_thesises($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_defended_thesises_header($t);

		$thesis_categories = array("seminaritöö", "BA", "MA");
		$data = $this->entry->val("defended_thesises");
		
		foreach($thesis_categories as $id => $thesis_category)
		{
			$t->define_data(array(
				"id" => $id,
				"years" => isset($data[$id]["years"]) ? $data[$id]["years"] : array(),
				"category" => $thesis_category
			));
		}

		return PROP_OK;
	}

	public function callback_opposed_thesises_years($row)
	{
		$year = substr($row["_this_cell"], -4);

		return html::textbox(array(
			"name" => "opposed_thesises[{$row["id"]}][years][{$year}]",
			"value" => !empty($row["years"][$year]) ? $row["years"][$year] : 0,
			"size" => 4,
		));
	}

	protected function _opposed_thesises_header($t)
	{
		$t->set_caption(t("Oponeeritud tööd"));

		$t->define_field(array(
			"name" => "category",
			"caption" => t("Oponeeritud töö kategooria")
		));
		$i = 2011;
		$t->define_field(array(
			"name" => "acad_year_$i",
			"caption" => t(sprintf("%s-%s", strval($i), substr(strval($i+1), 2))),
			"callback" => array($this, "callback_opposed_thesises_years"),
			"callb_pass_row" => true,
		));
	}

	public function _get_opposed_thesises($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_opposed_thesises_header($t);

		$thesis_categories = array(1 => "BA", "MA", "PhD");
		$data = $this->entry->val("opposed_thesises");
		
		foreach($thesis_categories as $id => $thesis_category)
		{
			$t->define_data(array(
				"id" => $id,
				"years" => isset($data[$id]["years"]) ? $data[$id]["years"] : array(),
				"category" => $thesis_category
			));
		}

		return PROP_OK;
	}

	protected function _defended_phd_thesises_header($t)
	{
		$t->set_caption(t("Juhendamisel kaitstud doktoritööd"));

		$t->define_field(array(
			"name" => "year",
			"caption" => t("Aasta"),
		));
		$t->define_field(array(
			"name" => "total",
			"caption" => t("Juhendamisel kaitstud tööde arv"),
			"callback" => array($this, "callback_defended_phd_thesises"),
			"callb_pass_row" => true,
		));
	}

	public function callback_defended_phd_thesises($row)
	{
		return html::textbox(array(
			"name" => "defended_phd_thesises[{$row["year"]}]",
			"value" => $row[$row["year"]],
			"size" => 4,
		));
	}

	public function _get_defended_phd_thesises($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_defended_phd_thesises_header($t);
		$data = $this->entry->val("defended_phd_thesises");

		for($i = 2004; $i < 2012; $i++)
		{
			$t->define_data(array(
				"year" => $i,
				$i => isset($data[$i]) ? $data[$i] : 0,
			));
		}

		return PROP_OK;
	}

	protected function _other_academic_activities_header($t)
	{
		$t->define_field(array(
			"name" => "activity",
			"caption" => NULL,
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => NULL,
			"callback" => array($this, "callback_other_academic_activities_amount"),
			"callb_pass_row" => true,
		));
	}

	public function callback_other_academic_activities_amount($row)
	{
		return html::textbox(array(
			"name" => "other_academic_activities[{$row["id"]}][amount]",
			"value" => $row["amount"],
			"size" => 4,
		));
	}

	public function _get_other_academic_activities($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_other_academic_activities_header($t);

		$activities = array(
			"Juhendatav praktika (AP)",
			"Vastuvõetud ümbrikueksamid 2008 (TAP)",
			"Töö kaitsmiskomisjonis 2009 (päevi)",
			"Juhitud vastuvõtu- või kaitsmiskomisjone",
			"Doktorinõukogu liikmelisus (1=jah, 0=ei)",
			"Väljatöötatud uusi õppekavu",
			"Koostatud õppevahendeid (kursuste jaoks, maht AP)"
		);
		$data = $this->entry->val("other_academic_activities");

		foreach($activities as $id => $activity)
		{
			$t->define_data(array(
				"id" => $id,
				"amount" => !empty($data[$id]["amount"]) ? $data[$id]["amount"] : 0,
				"activity" => $activity
			));
		}

		return PROP_OK;
	}

	protected function _publications_header($t)
	{
		$t->set_caption(t("Publikatsioonid, sh vastuvõetud"));

		$t->define_field(array(
			"name" => "category",
			"caption" => t("Publikatsiooni kategooria")
		));
		for($i = 2004; $i < 2012; $i++)
		{
			$t->define_field(array(
				"name" => "year_$i",
				"caption" => strval($i),
				"callback" => array($this, "callback_publications_years"),
				"callb_pass_row" => true,
			));
		}
		$t->define_field(array(
			"name" => "total",
			"caption" => t("Kokku"),
			"callback" => array($this, "callback_publications_total"),
			"callb_pass_row" => true,
		));
	}

	public function callback_publications_years($row)
	{
		$year = substr($row["_this_cell"], -4);
		return html::textbox(array(
			"name" => "publications[{$row["id"]}][years][{$year}]",
			"value" => !empty($row["years"][$year]) ? $row["years"][$year] : 0,
			"size" => 4,
		));
	}

	public function callback_publications_total($row)
	{
		return html::textbox(array(
			"name" => "publications[{$row["id"]}][total]",
			"value" => array_sum($row["years"]),
			"size" => 4,
			"disabled" => true,
		));
	}

	public function _get_publications($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_publications_header($t);

		$categories = $arr["obj_inst"]->manager()->get_publication_categories()->names();
		$data = $this->entry->val("publications");

		foreach($categories as $id => $category)
		{
			$t->define_data(array(
				"id" => $id,
				"years" => isset($data[$id]["years"]) ? $data[$id]["years"] : array(),
				"category" => $category,
			));
		}

		return PROP_OK;
	}

	protected function _previous_declaration_header($t)
	{
		$t->set_caption(t("Eelmise perioodi deklaratsioon"));

		$t->define_field(array(
			"name" => "category",
			"caption" => t("")
		));
		$t->define_field(array(
			"name" => "declared",
			"caption" => t("Deklareeritud"),
			"callback" => array($this, "callback_previous_declaration_declared"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "actual",
			"caption" => t("Tegelik"),
			"callback" => array($this, "callback_previous_declaration_actual"),
			"callb_pass_row" => true,
		));
	}

	public function callback_previous_declaration_declared($row)
	{
		return html::textbox(array(
			"name" => "previous_declaration[{$row["id"]}][declared]",
			"value" => $row["declared"],
			"size" => 4
		));
	}

	public function callback_previous_declaration_actual($row)
	{
		return html::textbox(array(
			"name" => "previous_declaration[{$row["id"]}][actual]",
			"value" => $row["actual"],
			"size" => 4
		));
	}

	public function _get_previous_declaration($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_previous_declaration_header($t);

		$categories = array(
			"Kontaktõppe kursused",
			"E-õppe kursused",
			"Juhendamisel kaitstud BA-tööd",
			"Juhendamisel kaitstud MA-tööd",
			"Juhendamisel kaitstud PhD-tööd",
			"Oponeeritud BA-tööd",
			"Oponeeritud MA-tööd",
			"Oponeeritud PhD-tööd",
			"Juhendatud praktika AP",
			"Töö vastuvõtukomisjonis (päevi)",
			"Töö kaitsmiskomisjonis (päevi)",
			"Juhitud komisjone",
			"Õppekoormus kokku",
		);
		$data = $this->entry->val("previous_declaration");

		foreach($categories as $id => $category)
		{
			$t->define_data(array(
				"id" => $id,
				"declared" => !empty($data[$id]["declared"]) ? $data[$id]["declared"] : 0,
				"actual" => !empty($data[$id]["actual"]) ? $data[$id]["actual"] : 0,
				"category" => $category,
			));
		}

		return PROP_OK;
	}

	protected function _premiums_header($t)
	{
		$t->set_caption(t("Eelmisel perioodil määratud igakuised lisatasud"));

		$t->define_field(array(
			"name" => "premium",
			"caption" => t("Lisatasu"),
			"callback" => array($this, "callback_premiums_premium"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Summa"),
			"callback" => array($this, "callback_premiums_amount"),
			"callb_pass_row" => true,
		));
	}

	public function callback_premiums_premium($row)
	{
		return html::textbox(array(
			"name" => "premiums[{$row["id"]}][premium]",
			"value" => $row["premium"]
		));
	}

	public function callback_premiums_amount($row)
	{
		return html::textbox(array(
			"name" => "premiums[{$row["id"]}][amount]",
			"value" => $row["amount"],
			"size" => 10
		));
	}

	public function _get_premiums($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_premiums_header($t);

		$premiums = $this->entry->val("premiums");

		$i = 0;
		foreach($premiums as $id => $premium)
		{
			if(empty($premium["premium"]))
			{
				continue;
			}

			$t->define_data(array(
				"id" => $id,
				"amount" => !empty($premium["amount"]) ? $premium["amount"] : 0,
				"premium" => $premium["premium"],
			));
			$i = max($i, $id + 1);
		}

		for($j = $i; $j < $i + 4; $j++)
		{
			$t->define_data(array(
				"id" => $j,
				"amount" => 0,
				"premium" => "",
			));
		}

		return PROP_OK;
	}

	public function callback_generate_scripts($arr)
	{
		load_javascript("jquery/plugins/jquery.calculation.js");
		load_javascript("applications/study_organisation/work_load_declaration.{$this->use_group}.js");
		
		switch($this->use_group)
		{
			case "work_loads":
				$rate_inst = new study_organisation_rate();
				
				$rates = array();
				foreach($arr["obj_inst"]->manager()->get_rates()->arr() as $rate)
				{
					$rates[$rate->id] = array(
						"id" => $rate->id,
						"type" => $rate->type,
						"category" => $rate->category,
						"applicables" => (array)$rate->applicables,
						"publication_categories" => (array)$rate->publication_categories,
						"thesis_categories" => (array)$rate->thesis_categories,
						"years" => (array)$rate->years
					);
				}


				$contact_learning = array("total" => 0);
				$e_learning = array("total" => 0);

				foreach(array_values($this->entry->val("contact_learning_courses")) as $course)
				{
					$contact_learning["total"] += aw_math_calc::string2float($course["points"]) * aw_math_calc::string2float($course["participants"]);
				}
				foreach(array_values($this->entry->val("e_learning_courses")) as $course)
				{
					$e_learning["total"] += aw_math_calc::string2float($course["points"]) * aw_math_calc::string2float($course["participants"]);
				}

				$publications = array();
				foreach($this->entry->val("publications") as $category => $publication)
				{
					$publications[$category] = $publication["years"];
				}

				$thesises = array(
					"defended" => array(),
					"opposed" => array(),
				);
				foreach($this->entry->val("defended_thesises") as $category => $years)
				{
					$thesises["defended"][$category] = $years["years"];
				}
				$thesises["defended"][3] = $this->entry->val("defended_phd_thesises");
				foreach($this->entry->val("opposed_thesises") as $category => $years)
				{
					$thesises["opposed"][$category] = $years["years"];
				}

				$professions = array();
				foreach($arr["obj_inst"]->manager()->get_professions()->arr() as $profession)
				{
					$professions[$profession->id] = array(
						"load" => $profession->load,
					);
				}

				$rates = json_encode($rates);
				$contact_learning = json_encode($contact_learning);
				$e_learning = json_encode($e_learning);
				$publications = json_encode($publications);
				$thesises =  json_encode($thesises);
				$professions = json_encode($professions);

				return "
					declaration = {
						points: {},
						configuration: {
							'rates': {$rates}
						},
						contact_learning: {$contact_learning},
						e_learning: {$e_learning},
						publications: {$publications},
						thesises: {$thesises},
						professions: {$professions}
					};
				";
		}

		return "";
	}

	public function callback_post_save($arr)
	{
		$o = $arr["obj_inst"]->get_declaration_entry_for_user();
		foreach($arr["request"] as $k => $v)
		{
			$o->set_meta($k, $v);
		}
		$o->save();
	}

	public function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_work_load_declaration" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_work_load_declaration` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_manager" === $field)
			{
				$this->db_add_col("aw_work_load_declaration", array(
					"name" => "aw_manager",
					"type" => "int"
				));
				$r = true;
			}
		}

		return $r;
	}
}
