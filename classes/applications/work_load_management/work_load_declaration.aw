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

			@property accounting_rates type=table no_caption=1 parent=wl_right

@groupinfo academic_activity caption="Akadeemiline tegevus"
@default group=academic_activity

	@groupinfo contact_and_e_learning caption="Kontakt- ja e-&otilde;pe" parent=academic_activity
	@default group=contact_and_e_learning

		@property cael_toolbar type=toolbar no_caption=1

		@property contact_learning_courses type=table no_caption=1

	@groupinfo e_learning caption="E-&otilde;pe" parent=academic_activity
	@default group=e_learning

		@property el_toolbar type=toolbar no_caption=1

		@property e_learning_courses type=table no_caption=1

	@groupinfo publications caption="Publikatsioonid, kaitstud/oponeeritud tööd jm akadeemiline tegevus" parent=academic_activity
	@default group=publications

		@property publications_toolbar type=toolbar no_caption=1

		@layout publications_split type=hbox width=50%:50%

			@layout publications_left type=vbox parent=publications_split

				@property publications type=table no_caption=1 parent=publications_left

			@layout publications_right type=vbox parent=publications_split

				@layout publications_right_upper type=vbox parent=publications_right

					@property defended_thesises type=table no_caption=1 parent=publications_right_upper

				@layout publications_right_middle type=hbox width=50%:50% parent=publications_right

					@property opposed_thesises type=table no_caption=1 parent=publications_right_middle

					@property defended_phd_thesises type=table no_caption=1 parent=publications_right_middle

				@property other_academic_activities type=table no_caption=1 parent=publications_right

@groupinfo previous_period caption="Eelmine periood"
@default group=previous_period

	@property pp_toolbar type=toolbar no_caption=1

	@layout pp_split type=hbox width=50%:50%

		@property previous_declaration type=table no_caption=1 parent=pp_split

		@property premiums type=table no_caption=1 parent=pp_split

*/

class work_load_declaration extends class_base
{
	public function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/work_load_management/work_load_declaration",
			"clid" => CL_WORK_LOAD_DECLARATION
		));
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
			"onclick" => "if(this.checked){ $('#professions_{$row["id"]}__load_').removeAttr('disabled') } else { $('#professions_{$row["id"]}__load_').attr('disabled', 'disabled'); }"
		));
	}

	public function callback_professions_load($row)
	{
		return html::textbox(array(
			"name" => "professions[{$row["id"]}][load]",
			"size" => 4,
			"disabled" => empty($row["active"]),
		));
	}

	public function _get_professions($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_professions_header($t);

		$professions = $arr["obj_inst"]->manager()->get_professions()->names();

		foreach($professions as $id => $profession)
		{
			$t->define_data(array(
				"id" => $id,
				"profession" => $profession
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
			"name" => "competence[{$row["id"]}][active]",
		));
	}

	public function _get_competence($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_competence_header($t);

		$competences = $arr["obj_inst"]->manager()->get_competences()->names();

		foreach($competences as $id => $competence)
		{
			$t->define_data(array(
				"id" => $id,
				"competence" => $competence
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
		));
	}

	public function _get_research_groups($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_research_groups_header($t);

		$research_groups = $arr["obj_inst"]->manager()->get_research_groups()->names();

		foreach($research_groups as $id => $research_group)
		{
			$t->define_data(array(
				"id" => $id,
				"research_group" => $research_group
			));
		}

		return PROP_OK;
	}

	protected function _accounting_rates_header($t)
	{
		$t->set_caption("Kehtestatud arvestusm&auml;&auml;rad");

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Arvestusm&auml;&auml;r"),
		));
		$t->define_field(array(
			"name" => "ma",
			"caption" => t("MA"),
		));
		$t->define_field(array(
			"name" => "tma",
			"caption" => t("tMA"),
		));
		$t->define_field(array(
			"name" => "phd",
			"caption" => t("PhD"),
		));
		$t->define_field(array(
			"name" => "prof_jt",
			"caption" => t("Prof/JT"),
		));
	}

	public function _get_accounting_rates($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_accounting_rates_header($t);

		$accounting_rates = array(
			array(
				"name" => t("Kontakttunnid (10 auditoorset tundi)"),
				"ma" => 20,
				"tma" => 25,
				"phd" => 30,
				"prof_jt" => 40,
			),
			array(
				"name" => t("E-&otilde;ppe kursus (EAP)"),
				"ma" => 25,
				"tma" => 30,
				"phd" => 40,
				"prof_jt" => 50,
			),
			array(
				"name" => t("Antavad tingainepunktid (100 TEAP kohta)"),
				"ma" => 6,
				"tma" => 8,
				"phd" => 10,
				"prof_jt" => 12,
			),
			array(
				"name" => t("Üliõpilastööde juhendamine viimasel 3 aastal (10 TEAP kohta)"),
				"ma" => 4,
				"tma" => 4,
				"phd" => 6,
				"prof_jt" => 6,
			),
			array(
				"name" => t("Doktoritööde juhendamine viimasel 3 aastal (töid)"),
				"phd" => 90,
				"prof_jt" => 120,
			),
			array(
				"name" => t("BA-tööde oponeerimine (töid)"),
				"ma" => 2,
				"tma" => 2,
				"phd" => 3,
				"prof_jt" => 4,
			),
			array(
				"name" => t("MA-tööde oponeerimine (töid)"),
				"ma" => 5,
				"tma" => 5,
				"phd" => 8,
				"prof_jt" => 10,
			),
			array(
				"name" => t("PhD-tööde oponeerimine (töid)"),
				"phd" => 3,
				"prof_jt" => 4,
			),
			array(
				"name" => t("Õppevahendi koostamine (1 EAP mahus kursuse kohta)"),
				"ma" => 6,
				"tma" => 6,
				"phd" => 10,
				"prof_jt" => 12,
			),
			array(
				"name" => t("Vastuvõtu- ja kaitsekomisjoni juht (komisjonide hulk)"),
				"tma" => 10,
				"phd" => 12,
				"prof_jt" => 15,
			),
			array(
				"name" => t("Kaitsmiskomisjoni liige (päevi)"),
				"ma" => 10,
				"tma" => 10,
				"phd" => 12,
				"prof_jt" => 15,
			),
			array(
				"name" => t("Vastuvõtukomisjoni liige (päevi)"),
				"ma" => 6,
				"tma" => 6,
				"phd" => 8,
				"prof_jt" => 10,
			),
			array(
				"name" => t("Doktorinõukogu liikmelisus"),
				"phd" => 20,
				"prof_jt" => 20,
			),
			array(
				"name" => t("Uue õppekava väljatöötamise juhtimine"),
				"phd" => 60,
				"prof_jt" => 80,
			),
			array(
				"name" => t("SF-teema juhtimine"),
				"ma" => 300,
				"tma" => 300,
				"phd" => 300,
				"prof_jt" => 300,
			),
			array(
				"name" => t("Osalus SF-teemas põhitäitjana, ETF grandi hoidmine"),
				"ma" => 150,
				"tma" => 150,
				"phd" => 150,
				"prof_jt" => 150,
			),
			array(
				"name" => t("1.1. ja 3.1. publikatsioonid"),
				"ma" => 150,
				"tma" => 150,
				"phd" => 150,
				"prof_jt" => 150,
			),
			array(
				"name" => t("2.1. monograafiad"),
				"ma" => 450,
				"tma" => 450,
				"phd" => 450,
				"prof_jt" => 450,
			),
			array(
				"name" => t("2.2. monograafiad"),
				"ma" => 210,
				"tma" => 210,
				"phd" => 210,
				"prof_jt" => 210,
			),
			array(
				"name" => t("1.2., 3.3., 5 kategooria publikatsioonid"),
				"ma" => 70,
				"tma" => 70,
				"phd" => 70,
				"prof_jt" => 70,
			),
			array(
				"name" => t("1.3., 3.2., 4 kategooria publikatsioonid"),
				"ma" => 40,
				"tma" => 40,
				"phd" => 40,
				"prof_jt" => 40,
			),
			array(
				"name" => t("Teaduslik kommenteeritud tõlge (artikkel/peatükk)"),
				"ma" => 15,
				"tma" => 15,
				"phd" => 15,
				"prof_jt" => 15,
			),
		);

		foreach($accounting_rates as $accounting_rate)
		{
			$t->define_data($accounting_rate);
		}

		return PROP_OK;
	}

	protected function _contact_learning_courses_header($t)
	{
		$t->set_caption(t("Kontakt&otilde;ppe kursused"));

		$t->define_field(array(
			"name" => "course",
			"caption" => t("Kursus"),
			"callback" => array($this, "callback_contact_learning_courses_course"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tunde"),
			"callback" => array($this, "callback_contact_learning_courses_hours"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points",
			"caption" => t("AP"),
			"callback" => array($this, "callback_contact_learning_courses_points"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "participants",
			"caption" => t("Osalejaid"),
			"callback" => array($this, "callback_contact_learning_courses_participants"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points_given",
			"caption" => t("Antavad AP"),
			"callback" => array($this, "callback_contact_learning_courses_points_given"),
			"callb_pass_row" => true,
		));
	}

	public function callback_contact_learning_courses_course($row)
	{
		return html::textbox(array(
			"name" => "contact_learning_courses[{$row["id"]}][course]",
		));
	}

	public function callback_contact_learning_courses_hours($row)
	{
		return html::textbox(array(
			"name" => "contact_learning_courses[{$row["id"]}][hours]",
			"size" => 4,
		));
	}

	public function callback_contact_learning_courses_points($row)
	{
		return html::textbox(array(
			"name" => "contact_learning_courses[{$row["id"]}][points]",
			"size" => 4,
		));
	}

	public function callback_contact_learning_courses_participants($row)
	{
		return html::textbox(array(
			"name" => "contact_learning_courses[{$row["id"]}][participants]",
			"size" => 4,
		));
	}

	public function callback_contact_learning_courses_points_given($row)
	{
		return html::textbox(array(
			"name" => "contact_learning_courses[{$row["id"]}][points_given]",
			"size" => 4,
			"disabled" => true
		));
	}

	public function _get_contact_learning_courses($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_contact_learning_courses_header($t);

		for($i = 0; $i < 6; $i++)
		{
			$t->define_data(array(
				"id" => $i
			));
		}

		return PROP_OK;
	}

	protected function _e_learning_courses_header($t)
	{
		$t->set_caption(t("E-&otilde;ppe kursused"));

		$t->define_field(array(
			"name" => "course",
			"caption" => t("Kursus"),
			"callback" => array($this, "callback_e_learning_courses_course"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points",
			"caption" => t("AP"),
			"callback" => array($this, "callback_e_learning_courses_points"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "participants",
			"caption" => t("Osalejaid"),
			"callback" => array($this, "callback_e_learning_courses_participants"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "points_given",
			"caption" => t("Antavad AP"),
			"callback" => array($this, "callback_e_learning_courses_points_given"),
			"callb_pass_row" => true,
		));
	}

	public function callback_e_learning_courses_course($row)
	{
		return html::textbox(array(
			"name" => "e_learning_courses[{$row["id"]}][course]",
		));
	}

	public function callback_e_learning_courses_points($row)
	{
		return html::textbox(array(
			"name" => "e_learning_courses[{$row["id"]}][points]",
			"size" => 4,
		));
	}

	public function callback_e_learning_courses_participants($row)
	{
		return html::textbox(array(
			"name" => "e_learning_courses[{$row["id"]}][participants]",
			"size" => 4,
		));
	}

	public function callback_e_learning_courses_points_given($row)
	{
		return html::textbox(array(
			"name" => "e_learning_courses[{$row["id"]}][points_given]",
			"size" => 4,
			"disabled" => true
		));
	}

	public function _get_e_learning_courses($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_e_learning_courses_header($t);

		for($i = 0; $i < 6; $i++)
		{
			$t->define_data(array(
				"id" => $i
			));
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
		for($i = 2006; $i < 2009; $i++)
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
		return html::textbox(array(
			"name" => "defended_thesises[{$row["id"]}][years][" . substr($row["_this_cell"], -4) . "]",
			"size" => 4,
		));
	}

	public function callback_defended_thesises_total($row)
	{
		return html::textbox(array(
			"name" => "defended_thesises[{$row["id"]}][total]",
			"size" => 4,
			"disabled" => true
		));
	}

	public function _get_defended_thesises($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_defended_thesises_header($t);

		$thesis_categories = array("seminaritöö", "BA", "MA");
		
		foreach($thesis_categories as $id => $thesis_category)
		{
			$t->define_data(array(
				"id" => $id,
				"category" => $thesis_category
			));
		}

		return PROP_OK;
	}

	public function callback_opposed_thesises_years($row)
	{
		return html::textbox(array(
			"name" => "opposed_thesises[{$row["id"]}][years][" . substr($row["_this_cell"], -4) . "]",
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
		$i = 2008;
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

		$thesis_categories = array("BA", "MA", "PhD");
		
		foreach($thesis_categories as $id => $thesis_category)
		{
			$t->define_data(array(
				"id" => $id,
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
			"callback" => array($this, "callback_defended_phd_thesises_total"),
			"callb_pass_row" => true,
		));
	}

	public function callback_defended_phd_thesises_total($row)
	{
		return html::textbox(array(
			"name" => "defended_phd_thesises[{$row["year"]}][total]",
			"size" => 4,
		));
	}

	public function _get_defended_phd_thesises($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_defended_phd_thesises_header($t);

		for($i = 2006; $i < 2009; $i++)
		{
			$t->define_data(array(
				"year" => $i,
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

		foreach($activities as $id => $activity)
		{
			$t->define_data(array(
				"id" => $id,
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
		for($i = 2004; $i < 2009; $i++)
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
		return html::textbox(array(
			"name" => "publications[{$row["id"]}][years][" . substr($row["_this_cell"], -4) . "]",
			"size" => 4,
		));
	}

	public function callback_publications_total($row)
	{
		return html::textbox(array(
			"name" => "publications[{$row["id"]}][total]",
			"size" => 4,
			"disabled" => true,
		));
	}

	public function _get_publications($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_publications_header($t);

		$categories = $arr["obj_inst"]->manager()->get_publication_categories()->names();

		foreach($categories as $id => $category)
		{
			$t->define_data(array(
				"id" => $id,
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
			"size" => 4
		));
	}

	public function callback_previous_declaration_actual($row)
	{
		return html::textbox(array(
			"name" => "previous_declaration[{$row["id"]}][actual]",
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

		foreach($categories as $id => $category)
		{
			$t->define_data(array(
				"id" => $id,
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
			"size" => 10
		));
	}

	public function _get_premiums($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_premiums_header($t);

		$premiums = array(
			"Õppekava juhtimine",
			"",
			"",
			"",
			"",
		);

		foreach($premiums as $id => $premium)
		{
			$t->define_data(array(
				"id" => $id,
				"premium" => $premium,
			));
		}

		return PROP_OK;
	}

	public function callback_generate_scripts($arr)
	{
		load_javascript("jquery/plugins/jquery.calculation.js");
		return "
			$(document).ready(function() {
				(function() {
					function sum_courses_points(o) {
						suffix = false;
						if(o.name.substr(-8) == '[points]') {
							suffix = 'points';
						} else if(o.name.substr(-14) == '[participants]') {
							suffix = 'participants';
						}
						if(suffix != false) {
							$('#' + o.id.replace(suffix, 'points_given')).calc('points * participants', {
								points: $('#' + o.id.replace(suffix, 'points')).val(),
								participants: $('#' + o.id.replace(suffix, 'participants')).val(),
							});
						}
					}

					$('input[name^=contact_learning_courses]').each(function(){
						sum_courses_points(this);
						$(this).keyup(function(){
							sum_courses_points(this);
						});
					});
					$('input[name^=e_learning_courses]').each(function(){
						sum_courses_points(this);
						$(this).keyup(function(){
							sum_courses_points(this);
						});
					});

					$('input[name^=publications]').keyup(function(){
						name = this.name.substr(0, this.name.length -6);
						$(\"input[name='\" + name.replace('years', 'total') + \"']\").val($(\"input[name^='\" + name + \"']\").sum());
					});
					$('input[name^=defended_thesises]').keyup(function(){
						name = this.name.substr(0, this.name.length -6);
						$(\"input[name='\" + name.replace('years', 'total') + \"']\").val($(\"input[name^='\" + name + \"']\").sum());
					});
				})();
			});
		";
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
