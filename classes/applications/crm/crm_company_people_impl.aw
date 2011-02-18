<?php

define("CRM_ALL_PERSONS_CAT",  "all");

class crm_company_people_impl extends class_base
{
	function crm_company_people_impl()
	{
		$this->init();
	}

	function _get_contact_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		if (!empty($arr["request"]["cat"]) and CRM_ALL_PERSONS_CAT !== $arr["request"]["cat"])
		{
			$tb->add_menu_item(array(
				'parent'=>'add_item',
				'text'=> t('T&ouml;&ouml;taja'),
				'link'=>aw_url_change_var(array(
					"action" => "add_employee",
					"id" => $arr["obj_inst"]->id(),
					"return_url" => get_ru(),
					"class" => "crm_company",
					"profession" => $arr["request"]["cat"]
				))
			));
		}

		//uus k6ne
		$tb->add_button(array(
			'name' => 'Kone',
			'img' => 'class_223.gif',
			'tooltip' => t('Tee k&otilde;ne'),
			'action' => 'submit_new_call'
		));

		//uus date
		$tb->add_button(array(
			'name' => 'Kohtumine',
			'img' => 'class_224.gif',
			'tooltip' => t('Uus kohtumine'),
			'action' => 'submit_new_meeting'
		));

		//uus task
		$tb->add_button(array(
			'name' => 'Toimetus',
			'img' => 'class_244.gif',
			'tooltip' => t('Uus toimetus'),
			'action' => 'submit_new_task'
		));

		$tb->add_separator();

		$tb->add_menu_button(array(
			'name' => 'Search',
			'img' => 'search.gif',
			'tooltip' => t('Otsi'),
			'action' => 'search_for_contacts'
		));

		$tb->add_menu_item(array(
			'parent'=>'Search',
			'text' => t('Isikuid'),
			'link'=> "javascript:submit_changeform('search_for_contacts')"
		));

		if(!empty($arr["request"]["contacts_search_show_results"]))
		{
			$tb->add_button(array(
				'name' => 'Salvesta',
				'img' => 'save.gif',
				'tooltip' => t('Lisa isikud organisatisooni'),
				'action' => 'save_contact_rels'
			));
		}

		$tb->add_separator();

		$tb->add_menu_button(array(
			"name" => "important",
			"img" => "important.png",
			"tooltip" => t("Olulisus"),
		));

		$tb->add_menu_item(array(
			"parent" => "important",
			"text" => t("M&auml;rgi oluliseks"),
			"action" => "mark_p_as_important",
		));

		$tb->add_menu_item(array(
			"parent" => "important",
			"text" => t("Eemalda olulisuse m&auml;rge"),
			"action" => "unmark_p_as_important",
		));

		$seti = new crm_settings();
		$sts = $seti->get_current_settings();
		if ($sts && $sts->prop("send_mail_feature"))
		{
			$tb->add_button(array(
				'name'=>'send_email',
				'tooltip'=> t('Saada kiri'),
				"img" => "mail_send.gif",
				'action' => 'send_mails',
			));
		}

		$tb->add_separator();

		$c = new popup_menu();
		$c->begin_menu("crm_co_ppl_filt");

		$c->add_item(array(
			"text" => t("T&uuml;hista"),
			"link" => aw_url_change_var("filt_p", null)
		));
		for($i = ord('A'); $i < ord("Z"); $i++)
		{
			$c->add_item(array(
				"text" => chr($i).(automatweb::$request->arg("filt_p") == chr($i) ? t(" (Valitud)") : "" ),
				"link" => aw_url_change_var("filt_p", chr($i))
			));
		}

		$tb->add_cdata(t("Vali filter:").$c->get_menu().(!empty($arr["request"]["filt_p"]) ? t("Valitud:").$arr["request"]["filt_p"] : "" ));
	}

	function _get_unit_listing_tree($arr)
	{
		if (automatweb::$request->arg("contact_search") == 1)
		{
			return PROP_IGNORE;
		}
		$tree_inst = $arr['prop']['vcl_inst'];
		$node_id = 0;
		$unit = isset($arr['request']['unit']) ? (int) $arr['request']['unit'] : 0;
		$cat = isset($arr['request']['cat']) ? (int) $arr['request']['cat'] : 0;

		$i = new crm_company();
		$i->active_node = is_oid($cat) ? $cat : $unit;
		$i->generate_tree(array(
			'tree_inst' => $tree_inst,
			'obj_inst' => $arr['obj_inst'],
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_SECTION',
			'attrib' => 'unit',
			'leafs' => true,
		));

		$nm = t("K&otilde;ik t&ouml;&ouml;tajad");
		$tree_inst->add_item(0, array(
			"id" => CRM_ALL_PERSONS_CAT,
			"name" => $cat === CRM_ALL_PERSONS_CAT ? "<b>".$nm."</b>" : $nm,
			"url" => aw_url_change_var(array(
				"cat" =>  CRM_ALL_PERSONS_CAT,
				"unit" =>  NULL,
			))
		));

		if (isset($_SESSION["crm"]["people_view"]) and $_SESSION["crm"]["people_view"] === "edit")
		{
			$tree_inst->set_root_name($arr["obj_inst"]->name());
			$tree_inst->set_root_icon(icons::get_icon_url(CL_CRM_COMPANY));
			$tree_inst->set_root_url(aw_url_change_var("cat", NULL, aw_url_change_var("unit", NULL)));
		}
	}

	function callb_human_name($arr)
	{
		$name = isset($arr["name"]) ? $arr["name"] : t("M&auml;&auml;ramata");

		if (isset($arr["firstname"]))
		{
			$name = $arr["firstname"];
		}

		if (isset($arr["lastname"]))
		{
			$name .= (isset($arr["firstname"]) ? " " : "") . $arr["lastname"];
		}

		return html::get_change_url(
			$arr["id"],
			array("return_url" => (empty($this->hr_tbl_return_url) ? get_ru() : $this->hr_tbl_return_url)),
			parse_obj_name($name)
		);
	}

	function _init_human_resources_table(aw_table $t, $fields = false)
	{
		$fields_data = array(
			array(
				"name" => "cal",
				"chgbgcolor" => "cutcopied",
				"caption" => t("&nbsp;"),
				"width" => 1
			),
			array(
				'name' => 'image',
				'caption' => t('&nbsp;'),
				"chgbgcolor" => "cutcopied",
				"align" => "center",
				"width" => 1
			),
			array(
				'name' => 'name',
				'caption' => t('Nimi'),
				'sortable' => '1',
				"chgbgcolor" => "cutcopied",
				'callback' => array($this, 'callb_human_name'),
				'callb_pass_row' => true,
			),
			array(
				'name' => 'phone',
				"chgbgcolor" => "cutcopied",
				'caption' => t('Telefon'),
				'sortable' => '1',
			),
			array(
				'name' => 'email',
				"chgbgcolor" => "cutcopied",
				'caption' => t('E-post'),
				'sortable' => '1',
			),
			array(
				'name' => 'work_relation',
				"chgbgcolor" => "legend",
				'caption' => t('T&ouml;&ouml;suhe'),
				'sortable' => '1',
			),
			array(
				'name' => 'authorized',
				"chgbgcolor" => "cutcopied",
				'caption' => t('Volitatud'),
				'sortable' => '1',
			)
		);

		foreach($fields_data as $field_data)
		{
			if($fields === false || in_array($field_data["name"], $fields))
			{
				$t->define_field($field_data);
			}
		}

		$t->define_chooser(array(
			'name'=>'check',
			'field'=>'id'
		));
		$t->set_default_sortby("name");
	}

	function _get_human_resources($arr)
	{
		if(!empty($arr['request']['contact_search']) || !empty($arr["request"]["prof_search"]))
		{
			return PROP_IGNORE;
		}

		if(!empty($arr["caller_ru"]))
		{
			$this->hr_tbl_return_url = $arr["caller_ru"];
		}

		$u = new user();
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_human_resources_table($t, isset($arr["prop"]["fields"]) ? $arr["prop"]["fields"] : false);
		$format = t('%s t&ouml;&ouml;tajad');
		$t->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$crmp = new crm_person();

		// to get those adding links work, I need
		// 1. id of my calendar
		// 2. relation type
		// alias_to_org oleks isiku id
		// reltype_org oleks vastava seose id

		$pl = new planner();
		$cal_id = $pl->get_calendar_for_user(array('uid'=>aw_global_get('uid')));

		// XXX: I should check whether $this->cal_id exists and only include those entries
		// when it does.

		// call : rel=9 : clid=CL_CRM_CALL
		// meeting : rel=8 : clid=CL_CRM_MEETING
		// task : rel=10 : clid=CL_TASK
		$persons = array();
		$professions = array();
		$sections = array();
		//if section present, i'll get all the professions

		//-------------------osakonna inimesed-------------
		if(!empty($arr['request']['unit']))
		{
			$tmp_obj = new object($arr['request']['unit']);
			$sections[] = $arr['request']['unit'];
			if(empty($arr['request']['cat'])) //kui miski amet v6i nii, siis leiab isikud hiljem
			{
				$worker_ol = $arr["obj_inst"]->get_employees(false, null, $tmp_obj);
				$persons = $worker_ol->ids();
			}
		}

		//----------------------- teatud ameti inimesed--------------------------------
		if(!empty($arr['request']['cat']) && $arr["request"]["cat"] !== CRM_ALL_PERSONS_CAT)
		{
			$tmp_obj = obj($arr['request']['cat'], array(), CL_CRM_PROFESSION);
			$worker_ol = $arr["obj_inst"]->get_employees(false, $tmp_obj);
			$persons = $worker_ol->ids();
			$professions = array($arr['request']['cat']);
		}

		//------------------------- ainult olulisteks m2rgitud inimesed-------------------
		if(empty($arr['request']['cat']) && empty($arr['request']['unit']))
		{
			$section_ol = $arr["obj_inst"]->get_sections();
			$sections = $section_ol->ids();
			$p = get_current_person();
			if($p)
			{
				$worker_ol = $p->get_important_persons($arr["obj_inst"]->id());
				$persons = $worker_ol->ids();
			}
		}

		//---------------------- k6ik asutuse inimesed------------------
		// kas siis kui tahetud k6iki v6i siis kui ei saanud yhtgi tulemust ja on m22ratud et sellisel juhul l2hevad k6ik
		if (isset($arr["request"]["cat"]) && $arr["request"]["cat"] === CRM_ALL_PERSONS_CAT || (isset($arr["request"]["all_if_empty"]) && $arr["request"]["all_if_empty"] && !($worker_ol && $worker_ol->count())))
		{
			$worker_ol = $arr["obj_inst"]->get_employees(false);
			$persons = $worker_ol->ids();
			$section_ol = $arr["obj_inst"]->get_sections();
			$sections = $section_ol->ids();

		}

		// get calendars for persons
		$pers2cal = $this->_get_calendars_for_persons($persons);
		foreach($persons as $person)
		{
			$tdata = array();
			$person = new object($person);
			$tdata["cutcopied"] = (isset($_SESSION["crm_copy_p"][$person->id()]) || isset($_SESSION["crm_cut_p"][$person->id()]) ? "#E2E2DB" : "");
			$tdata["cal"] = "";
			if (isset($pers2cal[$person->id()]))
			{
				$calo = obj($pers2cal[$person->id()]);
				$tdata["cal"] = html::href(array(
					"url" => html::get_change_url($calo->id(), array("return_url" => get_ru(), "group" => "views", "viewtype" => "week"))."#today",
					"caption" => html::img(array(
						"url" => icons::get_icon_url(CL_PLANNER),
						"border" => 0
					))
				));
			}

			$aol = new object_list(array(
				"class_id" => CL_CRM_AUTHORIZATION,
				"our_company" => $u->get_current_company(),
				"customer_company" => $arr["obj_inst"]->id(),
				"authorized_person" => $person->id()
			));
			$a_links = array();
			foreach($aol->arr() as $aut)
			{
				$a_links[] = html::href(array(
						"url" => html::get_change_url($aut->id()),
						"caption" => (strlen($aut->name()) > 0)?$aut->name():t("(Nimetu)"),
					));
			}
			$authoirization = join(", " , $a_links);
			$tdata["authorized"] = html::checkbox(array(
				"name" => "authorized[".$person->id()."]",
				"value" => 1,
				"checked" => 0,
				"onclick" => 'Javascript:window.open("'.html::get_new_url(
					CL_CRM_AUTHORIZATION,
					$person->id(),
					array(
						"return_url" => get_ru(),
						"person" => $person->id(),
						"our_company" => $u->get_current_company(),
						"customer_company" => $arr["obj_inst"]->id(),
						"return_after_save" => 1,
					)
				)
				.'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=800, width=720")',
			))." " .$authoirization;


			$rel_menu = new popup_menu();
			$rel_menu->begin_menu("crm_co_person_employment_rels_" . $person->id());
			$rels = $arr["obj_inst"]->get_work_relations(array(
				"employee" => $person->id(),
				"section" => empty($arr['request']['unit']) ? null : $arr['request']['unit'],
				"profession" => (empty($arr['request']['cat']) or $arr['request']['cat'] === CRM_ALL_PERSONS_CAT) ? null : $arr['request']['cat'],
			));
			$rels_count = $rels->count();
			$time = time();
			$active = false;

			if($rels_count)
			{
				$rel = $rels->begin();
				do
				{
					$rel_start = $rel->prop("start") > 1 ? aw_locale::get_lc_date($rel->prop("start"), aw_locale::DATE_SHORT_FULLYEAR) : t("(M&auml;&auml;ramata)");
					$rel_end = $rel->prop("end") > 1 ? aw_locale::get_lc_date($rel->prop("end"), aw_locale::DATE_SHORT_FULLYEAR) : t("(M&auml;&auml;ramata)");
					$active = ($active or (($rel->prop("end") > $time or $rel->prop("end") < 1) and ($rel->prop("start") < $time or $rel->prop("start") < 1)));
					$rel_menu->add_item(array(
						"text" => "{$rel_start} - {$rel_end}",
						"link" => html::get_change_url($rel->id(), array(
							"return_url" => get_ru()
						))
					));
				}
				while ($rel = $rels->next());
			}

			$rel_menu->add_item(array(
				"text" => t("Lisa"),
				"link" => html::get_new_url(
					CL_CRM_PERSON_WORK_RELATION,
					$person->id(),
					array(
						"return_url" => get_ru(),
						"employee" => $person->id(),
						"section" => empty($arr['request']['unit']) ? null : $arr['request']['unit'],
						"profession" => (empty($arr['request']['cat']) or $arr['request']['cat'] === CRM_ALL_PERSONS_CAT) ? null : $arr['request']['cat'],
						"employer" => $arr["obj_inst"]->id()
					)
				)
			));

			$tdata["work_relation"] = $rel_menu->get_menu() . "({$rels_count})";
			$tdata["id"] = $person->id();
			$tdata["phone"] = $person->get_phone($arr["obj_inst"]->id() , $sections);
			$tdata["email"] = $person->get_mail_tag($arr["obj_inst"]->id() , $sections);
			$tdata["firstname"] = $person->prop("firstname");
			$tdata["lastname"] = $person->prop("lastname");
			$tdata["name"] = $person->name();
			$tdata["image"] = $person->get_image_tag();
			$tdata["legend"] = $active ? "lightgreen" : "silver";
			$t->define_data($tdata);
		}
	}

	function _add_edit_stuff_to_table($arr)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->set_sortable(false);
		$parent = isset($arr["request"]["cat"]) ? $arr["request"]["cat"] : 0;
		if (!$parent)
		{
			$parent = isset($arr["request"]["unit"]) ? $arr["request"]["unit"] : 0;
		}

		if ($parent && $parent !== CRM_ALL_PERSONS_CAT)
		{
			$parent = obj($parent);
		}
		else
		{
			$parent = $arr["obj_inst"];
		}

		$section_img = html::img(array("url" => icons::get_icon_url(CL_CRM_SECTION), "border" => "0", "alt" => t("&Uuml;ksus")));
		$profession_img = html::img(array("url" => icons::get_icon_url(CL_CRM_PROFESSION), "border" => "0", "alt" => t("Amet")));

		if ($parent->is_a(CL_CRM_COMPANY) or $parent->is_a(CL_CRM_SECTION))
		{
			$sections = $this_o->get_sections($parent);
			if($sections->count())
			{
				$section = $sections->begin();
				$section_id = $section->id();

				do
				{
					$ccp = (isset($_SESSION["crm_cut_p"][$section_id]) ? "#E2E2DB" : "");
					// This produces an error if there are more than 2 words in the name.
					$table->define_data(array(
						"image" => $section_img,
						"name" => $section->name(),
						"id" => $section_id,
						"cutcopied" => $ccp
					));
				}
				while ($section = $sections->next());
			}

			$professions = $this_o->get_professions($parent);
			if($professions->count())
			{
				$profession = $professions->begin();
				$profession_id = $profession->id();

				do
				{
					$ccp = (isset($_SESSION["crm_cut_p"][$profession_id]) ? "#E2E2DB" : "");
					// This produces an error if there are more than 2 words in the name.
					$table->define_data(array(
						"image" => $profession_img,
						"name" => $profession->name(),
						"id" => $profession_id,
						"cutcopied" => $ccp
					));
				}
				while ($profession = $professions->next());
			}
		}
	}

	function _get_contacts_search_results($arr)
	{
		if(empty($arr['request']['contact_search']) && empty($arr['request']['contacts_search_show_results']))
		{
			return PROP_IGNORE;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			'sortable' => '1',
			'callback' => array($this, 'callb_human_name'),
			'callb_pass_row' => true,
		));
		$t->define_field(array(
			'name' => 'phone',
			'caption' => t('Telefon'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'email',
			'caption' => t('E-post'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'section',
			'caption' => t('&Uuml;ksused'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'rank',
			'caption' => t('Ametikohad'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'orgs',
			'caption' => t('Organisatsioonid'),
			'sortable' => '1',
		));

		$format = t('%s t&ouml;&ouml;tajate otsingu tulemused');
		$t->set_caption(sprintf($format, $arr['obj_inst']->name()));


		$search_params = array(
			'class_id' => CL_CRM_PERSON,
			'limit' => 50,
			'sort_by'=>'name',
			"lang_id" => array(),
			"site_id" => array()
		);

		if($arr['request']['contact_search_name'])
		{
			$search_params['name'] = '%'.urldecode($arr['request']['contact_search_name']).'%';
		}

		if($arr['request']['contact_search_firstname'])
		{
			$search_params['firstname'] = '%'.urldecode($arr['request']['contact_search_firstname']).'%';
		}

		if($arr['request']['contact_search_lastname'])
		{
			$search_params['lastname'] = '%'.urldecode($arr['request']['contact_search_lastname']).'%';
		}

		if($arr['request']['contact_search_code'])
		{
			$search_params['personal_id'] = '%'.urldecode($arr['request']['contact_search_code']).'%';
		}

		if($arr['request']['contact_search_ext_id_alphanum'])
		{
			$search_params['ext_id_alphanumeric'] = "%" . urldecode($arr['request']['contact_search_ext_id_alphanum']) . "%";
		}

		if($arr['request']['contact_search_ext_id'])
		{
			$search_params['ext_id'] = (int) urldecode($arr['request']['contact_search_ext_id']);
		}

		$ol = new object_list($search_params);

		$pl = new planner();
		$person = new crm_person();
		$cal_id = $pl->get_calendar_for_user(array('uid'=>aw_global_get('uid')));

		foreach($ol->arr() as $o)
		{
			$phones = $o->phones();
			$person_data['phone'] = join(",", $phones->names());
			$cos = array();
			$orgs = $o->get_all_orgs();
			foreach($orgs->arr() as $orgid)
			{
				$cos[] = html::href(array("url" => html::get_change_url($orgid->id()), "caption" => $orgid->name()));
			}

			$t->define_data(array(
				"firstname" => $o->prop("firstname"),
				"lastname" => $o->prop("lastname"),
				"name" => $o->prop('name'),
				"id" => $o->id(),
				"phone" => $person_data['phone'],
				"rank" => join(", ", $o->get_profession_names()),
				'section' => join(", ", $o->get_section_names()),
				'orgs' => join(", ", $cos),
				"oid" => $o->id(),
				"email" => join(", ", $o->get_all_mail_tags()),
			));
		}
	}

	function _get_personal_offers_toolbar($arr)
	{
		$toolbar = $arr["prop"]["vcl_inst"];
		$toolbar->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=>t('Uus')
		));

		$toolbar->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud t&ouml;&ouml;pakkumised'),
			'action' => 'delete_selected_objects',
			'confirm' => t("Kas oled kindel et soovid valitud t&ouml;&ouml;pakkumised kustudada?")
		));

		if($arr["request"]["cat"] && $arr["request"]["unit"] && $arr["request"]["cat"] !== CRM_ALL_PERSONS_CAT)
		{
			$alias_to =  $arr["request"]["unit"];
			$reltype = 4;
		}
		else
		{
			$alias_to = $arr["obj_inst"]->id();
			$reltype = 19;
		}

		$toolbar->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('T&ouml;&ouml;pakkumine'),
			'link'=>$this->mk_my_orb('new',array(
					'parent'=>$arr['obj_inst']->id(),
					'alias_to'=>$alias_to,
					'reltype'=> $reltype,
					'return_url'=>get_ru(),
					'cat' => $arr["request"]["cat"] !== CRM_ALL_PERSONS_CAT ? $arr["request"]["cat"] : NULL,
					'unit' => $arr["request"]["unit"],
					'org' => $arr['obj_inst']->id(),
			), CL_PERSONNEL_MANAGEMENT_JOB_OFFER)
		));
	}

	function _get_unit_listing_tree_personal($arr)
	{
		$tree_inst = $arr['prop']['vcl_inst'];
		$node_id = 0;

		$i = new crm_company();
		$i->active_node = (int)$arr['request']['unit'];
		$i->generate_tree(array(
			'tree_inst' => $tree_inst,
			'obj_inst' => $arr['obj_inst'],
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_SECTION',
			'attrib' => 'unit',
			'leafs' => true,
		));
	}

	function _get_personal_offers_table($arr)
	{
		$table = $arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "osakond",
			"caption" => t("Osakond"),
			"sortable" => "1",
		));

		$table->define_field(array(
			"name" => "ametinimi",
			"caption" => t("Ametikoht"),
			"sortable" => "1",
		));

		$table->define_field(array(
			"name" => "comments",
			"caption" => t("Kommentaar"),
			"sortable" => "1",
		));

		$table->define_field(array(
			"name" => "kehtiv_alates",
			"caption" => t("Kehtiv alates"),
			"sortable" => "1",
			"width" => 80,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.y",
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "kehtiv_kuni",
			"caption" => t("Kehtiv kuni"),
			"sortable" => "1",
			"width" => 80,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.y",
			"align" => "center",
		));

		$table->define_chooser(array(
			"name" => "select",
			"field" => "job_id",
			"caption" => t("X"),
			"width" => 20,
			"align" => "center"
		));

		$format = t('%s t&ouml;&ouml;pakkumised');
		$table->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$section_cl = new crm_section();

		if(is_oid($arr['request']['unit']))
		{
			$jobs_ids = $section_cl->get_section_job_ids_recursive($arr['request']['unit']);
		}
		else
		{
			$jobs_ids = $section_cl->get_all_org_job_ids($arr["obj_inst"]->id());
			$professions = $section_cl->get_all_org_professions($arr["obj_inst"]->id(), true);
		}

		if(!$jobs_ids)
		{
			return;
		}

		$job_obj_list = new object_list(array(
			"oid" => array_keys($jobs_ids),
			"profession" => $arr["request"]["cat"] !== CRM_ALL_PERSONS_CAT ? $arr["request"]["cat"] : NULL,
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER
		));
		$job_obj_list = $job_obj_list->arr();
		foreach ($job_obj_list as $job)
		{
			if($arr['request']['unit'])
			{
				$professions = $section_cl->get_professions($arr['request']['unit'], true);
			}

			if(!$professions[$job->prop("profession")])
			{
				$professin_cap = t("M&auml;&auml;ramata");
			}
			else
			{
				$professin_cap = $professions[$job->prop("profession")];
			}

			$table->define_data(array(
				"osakond" => $jobs_ids[$job->id()],
				"kehtiv_kuni" => $job->prop("deadline"),
				"ametinimi" => html::href(array(
					"caption" => $professin_cap,
					"url" => $this->mk_my_orb("change", array("id" =>$job->id()), CL_PERSONNEL_MANAGEMENT_JOB_OFFER),
				)),
				"kehtiv_alates" => $job->prop("beginning"),
				"job_id" => $job->id(),
				"comments" => $job->prop("comment"),
			));
		}
	}

	function _get_personal_candidates_toolbar($arr)
	{
		$toolbar = $arr["prop"]["vcl_inst"];
		$toolbar->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud t&ouml;&ouml;pakkumised'),
		));
	}

	function _get_unit_listing_tree_candidates($arr)
	{
		$tree_inst = $arr['prop']['vcl_inst'];
		$node_id = 0;
		$i = new crm_company();
		$i->active_node = isset($arr['request']['unit']) ? (int) $arr['request']['unit'] : 0;

		$i->generate_tree(array(
			'tree_inst' => $tree_inst,
			'obj_inst' => $arr['obj_inst'],
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_SECTION',
			'attrib' => 'unit',
			'leafs' => true,
		));
	}

	function _get_personal_candidates_table($arr)
	{
		$table = $arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "person_name",
			"caption" => t("Kandideerija nimi"),
			"sortable" => "1",
		));

		$table->define_field(array(
			"name" => "ametikoht",
			"caption" => t("Ametikoht"),
			"sortable" => "1",
		));

		$table->define_field(array(
			"name" => "osakond",
			"caption" => t("Osakond"),
			"sortable" => "1",
		));

		$format = t('%ssse t&ouml;&ouml;le kandideerijad');
		$table->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$section_cl = new crm_section();

		if(!empty($arr['request']['unit']))
		{
			$jobs_ids = $section_cl->get_section_job_ids_recursive($arr['request']['unit']);
		}
		else
		{
			$jobs_ids = $section_cl->get_all_org_job_ids($arr["obj_inst"]->id());
			$professions = $section_cl->get_all_org_professions($arr["obj_inst"]->id(), true);
		}

		if(!$jobs_ids)
		{
			return;
		}

		$candidate_conns = new connection();
		$candidate_conns = $candidate_conns->find(array(
        	"from" => array_keys($jobs_ids),
        	"to.class_id" => CL_CRM_PERSON,
        	"reltype" => 66666, //RELTYPE_CANDIDATE
		));

		$professions = $section_cl->get_all_org_professions($arr["obj_inst"]->id(), true);

		foreach ($candidate_conns as $candidate_conn)
		{
			$table->define_data(array(
				"person_name" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $candidate_conn['to']), CL_CRM_PERSON),
					"caption" => $candidate_conn['to.name'],
				)),
				"ametikoht" => $candidate_conn['from.name'],
				"osakond" => $jobs_ids[$candidate_conn['from']],
			));
		}
	}

	function _get_contact_search_desc($arr)
	{
		$arr["prop"]["value"] = "<span style=\"border: 1px black;\"><b>" . t("Otsi isikuid") . "</b>";
	}

	function _get_calendars_for_persons($persons)
	{
		$ret = array();

		$c = new connection();
		$cs = $c->find(array(
			"from.class_id" => CL_USER,
			"to.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PERSON",
			"to" => $persons
		));

		$users = array();
		$u2p = array();
		foreach($cs as $c)
		{
			$users[] = $c["from"];
			$u2p[$c["from"]] = $c["to"];
		}

		$c = new connection();
		$owners = $c->find(array(
			"from.class_id" => CL_PLANNER,
			"to.class_id" => CL_USER,
			"to" => $users
		));

		foreach($owners as $owner)
		{
			if (isset($u2p[$owner["to"]]))
			{
				$ret[$u2p[$owner["to"]]] = $owner["from"];
			}
		}

		return $ret;
	}

	function _get_cedit_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$alias_to = $arr['obj_inst']->id();
		if(!empty($arr['request']['unit']))
		{
			$alias_to = (int) $arr['request']['unit'];
		}

		if (!empty($arr["request"]["cat"]) and CRM_ALL_PERSONS_CAT !== $arr["request"]["cat"])
		{
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text"=> t("T&ouml;&ouml;taja"),
				"link" => $this->mk_my_orb("add_employee", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
						"save_autoreturn" => "1",
						"profession" => $arr["request"]["cat"]
					),
					"crm_company"
				)
			));
		}

		if (empty($arr["request"]["cat"]) or $arr["request"]["cat"] !== CRM_ALL_PERSONS_CAT)
		{
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text"=> t("&Uuml;ksus"),
				"link" => $this->mk_my_orb("add_section", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
						"save_autoreturn" => "1",
						"parent_section" => isset($arr["request"]["unit"]) ? $arr["request"]["unit"] : ""
					),
					"crm_company"
				)
			));
		}

		$tb->add_menu_item(array(
			"parent" => "add_item",
			"text"=> t("Ametikoht"),
			"link" => $this->mk_my_orb("add_profession", array(
					"id" => $arr["obj_inst"]->id(),
					"return_url" => get_ru(),
					"save_autoreturn" => "1",
					"section" => isset($arr["request"]["unit"]) ? $arr["request"]["unit"] : ""
				),
				"crm_company"
			)
		));

		//  search and add employee from existing persons in database
		if (!empty($arr["request"]["cat"]))
		{
			$url = $this->mk_my_orb("do_search", array(
				"clid" => CL_CRM_PERSON,
				"pn" => "sbt_data_add_employee"
			), "popup_search");
			$tb->add_button(array(
				'name' => 'Search',
				'img' => 'search.gif',
				"tooltip" => t("Lisa t&ouml;&ouml;taja olemasolevate isikute hulgast"),
				'link' => '#',
				'url' => '#',
				"onClick" => html::popup(array(
					"url" => $url,
					"resizable" => true,
					"scrollbars" => "auto",
					"height" => 500,
					"width" => 700,
					"no_link" => true,
					"quote" => "'"
				))
			));
		}

		//delete button
		$tb->add_menu_button(array(
			'name'=>'delete_item',
			'tooltip'=> t('Kustuta'),
			"img" => "delete.gif"
		));

		$tb->add_menu_item(array(
			"parent" => "delete_item",
			"text" => t("L&otilde;peta t&ouml;&ouml;suhted"),
			'name' => 'del_rels',
			'img' => 'delete.gif',
			"confirm" => t("Oled kindel et soovid valitud isikutega t&ouml;&ouml;suhted l&otilde;petada?"),
			"action" => "submit_delete_relations"
		));

		$tb->add_menu_item(array(
			"parent" => "delete_item",
			"text" => t("Kustuta isikud s&uuml;steemist"),
			'name' => 'del_objs',
			'img' => 'delete.gif',
			"confirm" => t("Oled kindel et soovid kustutada valitud t&ouml;&ouml;tajad?"),
			'action' => 'submit_delete_ppl'
		));

		$tb->add_separator();

		$tb->add_button(array(
			"name" => "cut",
			"img" => "cut.gif",
			"tooltip" => t("L&otilde;ika"),
			"action" => "cut_p",
		));

		$tb->add_button(array(
			"name" => "copy",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri"),
			"action" => "copy_p",
		));

		if (isset($_SESSION["crm_cut_p"]) and is_array($_SESSION["crm_cut_p"]) or isset($_SESSION["crm_copy_p"]) and is_array($_SESSION["crm_copy_p"]))
		{
			$tb->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"tooltip" => t("Kleebi"),
				"action" => "paste_p",
			));
		}

		$tb->add_separator();

		$tb->add_menu_button(array(
			"name" => "important",
			"img" => "important.png",
			"tooltip" => t("Olulisus"),
		));

		$tb->add_menu_item(array(
			"parent" => "important",
			"text" => t("M&auml;rgi oluliseks"),
			"action" => "mark_p_as_important",
		));

		$tb->add_menu_item(array(
			"parent" => "important",
			"text" => t("Eemalda olulisuse m&auml;rge"),
			"action" => "unmark_p_as_important",
		));
	}

	function _get_cedit_tree($arr)
	{
		$tree = $arr['prop']['vcl_inst'];
		$this_o = $arr["obj_inst"];

		// determine selected category and profession
		if(!empty($arr['request']['cat']))
		{
			$cat = (int) $arr["request"]["cat"];
			$unit = 0;
			$selected_item = $cat;
		}
		elseif (!empty($arr['request']['unit']))
		{
			$cat = 0;
			$unit = (int) $arr['request']['unit'];
			$selected_item = $unit;
		}
		else
		{
			$cat = $unit = $selected_item = 0;
		}

		// add sections to tree
		$sections = new object_list(array(
			"class_id" => CL_CRM_SECTION,
			"organization" => $this_o->id()
		));

		if($sections->count())
		{
			$section = $sections->begin();
			do
			{
				$parent = $section->prop("parent_section") ? $section->prop("parent_section") : 0;
				$tree->add_item($parent, array(
					"id" => (int) $section->id(),
					"name" => $section->name(),
					"iconurl" => icons::get_icon_url(CL_MENU),
					// "iconurl" => icons::get_icon_url(CL_CRM_SECTION),
					"url" => aw_url_change_var(array(
						"unit" => $section->id(),
						"cat" => null
					))
				));
			}
			while ($section = $sections->next());
		}

		// add professions to tree
		$professions = new object_list(array(
			"class_id" => CL_CRM_PROFESSION,
			"organization" => $this_o->id()
		));

		if($professions->count())
		{
			$profession = $professions->begin();

			do
			{
				$profession_name = $profession->prop("name_in_plural") ? $profession->prop("name_in_plural") : $profession->prop_str("name");
				$parent = $profession->prop("parent_section") ? $profession->prop("parent_section") : 0;
				$tree->add_item($parent, array(
					"id" => (int) $profession->id(),
					"name" => $profession_name,
					"iconurl" => icons::get_icon_url(CL_CRM_PROFESSION),
					"url" => aw_url_change_var(array(
						"cat" => $profession->id(),
						"unit" => null
					))
				));
			}
			while ($profession = $professions->next());
		}

		// all employees node
		$nm = t("K&otilde;ik t&ouml;&ouml;tajad");
		$tree->add_item(0, array(
			"id" => CRM_ALL_PERSONS_CAT,
			"name" => $cat === CRM_ALL_PERSONS_CAT ? "<b>".$nm."</b>" : $nm,
			"url" => aw_url_change_var(array(
				"cat" => CRM_ALL_PERSONS_CAT,
				"unit" => null
			))
		));

		// configure tree
		$tree->set_selected_item($selected_item);
		$tree->set_root_name($arr["obj_inst"]->name());
		$tree->set_root_icon(icons::get_icon_url(CL_CRM_COMPANY));
		$tree->set_root_url(aw_url_change_var("cat", NULL, aw_url_change_var("unit", NULL)));
	}

	function _get_cedit_table($arr)
	{
		$this->_add_edit_stuff_to_table($arr);
		$this->_get_human_resources($arr);
	}
}
