<?php

/** Human resources and organization structure management module/interface class **/
class crm_company_employees_view extends class_base
{
	const COLOUR_WORKREL_ACTIVE = "lightgreen";
	const COLOUR_WORKREL_INACTIVE = "silver";

	const REQVAL_ALL_SELECTION = "-1"; // value for all items selection (in treeviews e.g.). should be integer, explicit type casting used extesively
	const REQVAR_NODE = "es_c"; // request parameter name for organization section or profession
	const REQVAR_NAME = "es_n"; // request parameter name for name search
	const REQVAR_EMPLOYMENT_STATUS = "es_s"; // request parameter name for status

	const REQVAR_EMPLOYMENT_GENDER = "es_g"; // request parameter name for sex
	const REQVAR_EMPLOYMENT_ADDRESS = "es_a"; // request parameter name for address
	const REQVAR_EMPLOYMENT_EMAIL = "es_e"; // request parameter name for e-mail
	const REQVAR_EMPLOYMENT_AGEFROM = "es_agefrom"; // request parameter name for age from
	const REQVAR_EMPLOYMENT_AGETO = "es_ageto"; // request parameter name for age to
	const REQVAR_EMPLOYMENT_COUNTY = "es_county"; // request parameter name for county
	const REQVAR_EMPLOYMENT_CITY = "es_city"; // request parameter name for city
	const REQVAR_EMPLOYMENT_INDEX = "es_index"; // request parameter name for postal index
	const REQVAR_EMPLOYMENT_CITIZENSHIP = "es_citizenship"; // request parameter name for citizenship
	const REQVAR_EMPLOYMENT_CODE = "es_code"; // request parameter name for personal code


	const CLIPBOARD_DATA_VAR = "awcb_organization_structure_selection_clipboard";
	const CLIPBOARD_FROM_CAT_VAR = "awcb_organization_structure_old_parent_clipboard";
	const CLIPBOARD_ACTION_VAR = "awcb_organization_structure_clipboard_action";
	const CUTCOPIED_COLOUR = "silver";

	// these hold items selected in org structure tree
	private $selected_item_id = 0;
	private $selected_object;

	private $search_args = array(self::REQVAR_NODE, self::REQVAR_NAME);

	public function crm_company_employees_view()
	{
		$this->init();
	}

	public function set_request(aw_request $request)
	{
		parent::set_request($request);
//arr($this->req->arg(self::REQVAR_NODE));
		if (is_oid($this->req->arg(self::REQVAR_NODE)))
		{
			$this->selected_item_id = (int) $this->req->arg(self::REQVAR_NODE);

			try
			{
				$this->selected_object = new object($this->selected_item_id);
			}
			catch (awex_obj $e)
			{
				$this->show_error_text("Valitud &uuml;ksusele/ametile puudub juurdep&auml;&auml;s");
			}
		}
	}

	public function _get_es_s(&$arr)
	{
		$arr["prop"]["options"] = crm_employee_search::get_employment_status_options();
		unset($arr["prop"]["options"][crm_employee_search::EMPLOYMENT_STATUS_PROSPECTIVE]);

		if (!isset($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] = crm_employee_search::EMPLOYMENT_STATUS_ACTIVE;
		}
		return class_base::PROP_OK;
	}

	public function _get_es_g(&$arr)
	{
		$arr["prop"]["options"] = crm_employee_search::get_employment_gender_options();

		if (!isset($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] = crm_employee_search::EMPLOYMENT_GENDER_ALL;
		}
		return class_base::PROP_OK;
	}

	public function _get_hrm_toolbar($arr)
	{
		$r = class_base::PROP_OK;
		$hrm_toolbar = $arr["prop"]["vcl_inst"];

		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "add_item",
			"icon" => "add",
			"tooltip"=> t("Uus")
		));

		// employee add only if a profession selected
		if ($this->selected_object and $this->selected_object->is_a(crm_profession_obj::CLID))
		{
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text"=> t("T&ouml;&ouml;taja"),
				"link" => $this->mk_my_orb("add_employee", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
						"profession" => $this->selected_object->id()
					),
					"crm_company"
				)
			));
		}

		// parent for section and profession add buttons
		$parent = ($this->selected_object and $this->selected_object->is_a(crm_section_obj::CLID)) ? $this->selected_object->id() : "";

		// section add only if profession not selected
		if (!$this->selected_object or !$this->selected_object->is_a(crm_profession_obj::CLID))
		{
			$parent = ($this->selected_object and $this->selected_object->is_a(crm_section_obj::CLID)) ? $this->selected_object->id() : "";
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text"=> t("&Uuml;ksus"),
				"link" => $this->mk_my_orb("add_section", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
						"parent_section" => $parent
					),
					"crm_company"
				)
			));
		}

		//  search and add employee from existing persons in database, only when a profession is selected
		if ($this->selected_object and $this->selected_object->is_a(crm_profession_obj::CLID))
		{
			$url = $this->mk_my_orb("do_search", array(
				"clid" => crm_person_obj::CLID,
				"pn" => "add_existing_employee_oid"
			), "popup_search");

			$tb->add_button(array(
				"name" => "Search",
				"icon" => "magnifier",
				"tooltip" => t("Lisa t&ouml;&ouml;taja olemasolevate isikute hulgast"),
				"link" => "#",
				"url" => "#",
				"onclick" => html::popup(array(
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


		if ($this->selected_object and $this->selected_object->is_a(crm_section_obj::CLID))
		{

			$url = $this->mk_my_orb("do_search", array(
				"clid" => crm_profession_obj::CLID,
				"pn" => "add_existing_profession_oid"
			), "popup_search");

			$tb->add_button(array(
				"name" => "Search",
				"icon" => "magnifier",
				"tooltip" => t("Lisa amet olemasolevate ametite hulgast"),
				"link" => "#",
				"url" => "#",
				"onclick" => html::popup(array(
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

		// profession add, no specific item selection in tree required
		$tb->add_menu_item(array(
			"parent" => "add_item",
			"text"=> t("Ametikoht"),
			"link" => $this->mk_my_orb("add_profession", array(
					"id" => $arr["obj_inst"]->id(),
					"return_url" => get_ru(),
					"section" => $parent
				),
				"crm_company"
			)
		));

		$tb->add_save_button();

		$tb->add_separator();

		$tb->add_button(array(
			"name" => "cut",
			"icon" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"action" => "cut"
		));

		if (aw_session::get(self::CLIPBOARD_DATA_VAR))
		{
			$tb->add_button(array(
				"name" => "paste",
				"icon" => "paste",
				"tooltip" => t("Kleebi"),
				"action" => "paste"
			));
		}

		//delete button
		$tb->add_button(array(
			"name"=>"delete_item",
			"tooltip"=> t("Kustuta valitud isik(ud)/&uuml;ksus(ed)/amet(id)"),
			"confirm" => t("Oled kindel et soovid kustutada valitud t&ouml;&ouml;tajad?"),
			"action" => "delete_objects",
			"icon" => "delete"
		));

		// end work relations btn
		$tb->add_button(array(
			"parent" => "delete_item",
			"tooltip" => t("L&otilde;peta t&ouml;&ouml;suhted"),
			"name" => "del_rels",
			"icon" => "link_delete",
			"confirm" => t("Oled kindel et soovid valitud isikutega t&ouml;&ouml;suhted l&otilde;petada?"),
			"action" => "submit_delete_relations"
		));

		return $r;
	}

	public function _get_organization_structure_tree(&$arr)
	{
		$r = class_base::PROP_OK;
		$organization_structure_tree = $arr["prop"]["vcl_inst"];
		$organization_o = $arr["obj_inst"];
		$organization_structure_tree->set_type(treeview::TYPE_JS);

		$item_url_base = $this->req->get_uri(); // url in which the tree node variable will be changed on iteration over items
		$this->clear_search_args($item_url_base); // when clicking on a tree node, all other search parameters are cleared

		$data_url = new aw_uri($this->mk_my_orb("get_organization_tree_nodes", array("id" => $organization_o->id(), "url" => $item_url_base->get())));
		$organization_structure_tree->set_data_source_url($data_url);

		$selected = isset($arr["request"][self::REQVAR_NODE]) ? $arr["request"][self::REQVAR_NODE] : "";
		$organization_structure_tree->set_selected_item($selected);
		return $r;
	}

	/**
		@attrib name=get_organization_tree_nodes
		@param id required type=oid acl=view
		@param url required type=string
		@param node optional default=-1
			The id of the parent node for which the children will be returned.
	**/
	public function get_organization_tree_nodes($arr)
	{
		$organization_o = obj($arr["id"], array(), crm_company_obj::CLID);
		$parent = (isset($arr["node"]) and $this->can("view", $arr["node"])) ? new object($arr["node"]) : $organization_o;
		$url = new aw_uri($arr["url"]);
		$data = array(); // tree items' data that will eventually be encoded to json and sent to the browser

		// add sections
		$sections = $organization_o->get_sections($parent);
		if($sections->count())
		{
			$section = $sections->begin();

			do
			{
				$url->set_arg(self::REQVAR_NODE, $section->id());
				$data[] = array(
					"data" => array(
						"title" => $section->prop_str("name"),
						"icon" => icons::get_icon_url($section->class_id())
					),
					"attr" => array(
						"id" => $section->id(),
						"url" => $url->get()
					),
					"state" => "closed"
				);
			}
			while ($section = $sections->next());
		}

		// add professions
		$professions = $organization_o->get_professions($parent);
		if($professions->count())
		{
			$profession = $professions->begin();

			do
			{
				$url->set_arg(self::REQVAR_NODE, $profession->id());
				$data[] = array(
					"data" => array(
						"title" => $profession->prop_str("name"),
						"icon" => icons::get_icon_url($profession->class_id())
					),
					"attr" => array(
						"id" => $profession->id(),
						"url" => $url->get()
					)
				);
			}
			while ($profession = $professions->next());
		}

		// if top level request
		if ($parent === $organization_o)
		{
			// add root node
			$url->set_arg(self::REQVAR_NODE, $organization_o->id());
			$data = array(array(
				"data" => array(
					"title" => $organization_o->prop_str("name"),
					"icon" => icons::get_icon_url($organization_o->class_id())
				),
				"attr" => array(
					"id" => $organization_o->id(),
					"url" => $url->get()
				),
				"state" => "open",
				"children" => $data
			));

			// add all employees node
			$url->set_arg(self::REQVAR_NODE, self::REQVAL_ALL_SELECTION);
			$data[] = array(
				"data" => array(
					"title" => html_entity_decode(t("K&otilde;ik t&ouml;&ouml;tajad"),ENT_COMPAT,languages::USER_CHARSET),
					"icon" => icons::get_std_icon_url("group")
				),
				"attr" => array(
					"id" => self::REQVAL_ALL_SELECTION,
					"url" => $url->get()
				)
			);
		}

		// ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		exit(json_encode($data));
	}

	public function _get_employees_table(&$arr)
	{
		$r = class_base::PROP_OK;
		$organization_o = $arr["obj_inst"];
		$organization_oid = $organization_o->id();
		$employees_table = $arr["prop"]["vcl_inst"];
		$this->define_employees_table($employees_table);

		$gender_options = crm_employee_search::get_employment_gender_options();

		$employee_search = new crm_employee_search();

		try
		{
			$employee_search->employer = $organization_o;
			$section = null;
			$search_params_set = false;

			// org unit
			if ($this->selected_object)
			{
				if ($this->selected_object->is_a(crm_section_obj::CLID))
				{
					$employee_search->section = $this->selected_object;
					$section = $this->selected_item_id;
					$search_params_set = true;
				}
				elseif ($this->selected_object->is_a(crm_profession_obj::CLID))
				{
					$employee_search->profession = $this->selected_object;
					$section = $this->selected_object->prop("company_section");
					$search_params_set = true;
				}
			}
			elseif (isset($arr["request"][self::REQVAR_NODE]) and self::REQVAL_ALL_SELECTION === $arr["request"][self::REQVAR_NODE])
			{
				$search_params_set = true;
			}

			// searched by name
			if (!empty($arr["request"][self::REQVAR_NAME]))
			{
				$employee_search->name = $arr["request"][self::REQVAR_NAME];
				$search_params_set = true;
			}

			// searched by sex
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_GENDER]))
			{
				$employee_search->gender = $arr["request"][self::REQVAR_EMPLOYMENT_GENDER];
				$search_params_set = true;
			}

			// searched by address
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_ADDRESS]))
			{
				$employee_search->address = $arr["request"][self::REQVAR_EMPLOYMENT_ADDRESS];
				$search_params_set = true;
			}

			// searched by address
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_COUNTY]))
			{
				$employee_search->county = $arr["request"][self::REQVAR_EMPLOYMENT_COUNTY];
				$search_params_set = true;
			}
			// searched by address
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_CITY]))
			{
				$employee_search->city = $arr["request"][self::REQVAR_EMPLOYMENT_CITY];
				$search_params_set = true;
			}
			// searched by address
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_INDEX]))
			{
				$employee_search->index = $arr["request"][self::REQVAR_EMPLOYMENT_INDEX];
				$search_params_set = true;
			}
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_CITIZENSHIP]))
			{
				$employee_search->citizenship = $arr["request"][self::REQVAR_EMPLOYMENT_CITIZENSHIP];
				$search_params_set = true;
			}
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_CODE]))
			{
				$employee_search->personal_code = $arr["request"][self::REQVAR_EMPLOYMENT_CODE];
				$search_params_set = true;
			}
			// searched by e-mail
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_EMAIL]))
			{
				$employee_search->email = $arr["request"][self::REQVAR_EMPLOYMENT_EMAIL];
				$search_params_set = true;
			}

			// searched by age
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_AGEFROM]))
			{
				$employee_search->agefrom = $arr["request"][self::REQVAR_EMPLOYMENT_AGEFROM];
				$search_params_set = true;
			}
			if (!empty($arr["request"][self::REQVAR_EMPLOYMENT_AGETO]))
			{
				$employee_search->ageto = $arr["request"][self::REQVAR_EMPLOYMENT_AGETO];
				$search_params_set = true;
			}

			if ($search_params_set)
			{
				// set employment status (if search parameter not specified, display only active -- active is default)
				$employee_search->employment_status = !empty($arr["request"][self::REQVAR_EMPLOYMENT_STATUS]) ? (int) $arr["request"][self::REQVAR_EMPLOYMENT_STATUS] : crm_employee_search::EMPLOYMENT_STATUS_ACTIVE;

				$clipboard = (array) aw_session::get(self::CLIPBOARD_DATA_VAR);

				// get employees and fill table
				
				$employee_oids = $employee_search->get_oids();
				foreach ($employee_oids as $employee_oid => $work_relations)
				{
					$employee = new object($employee_oid);

					// compose work relations menu
					$rel_menu = new popup_menu();
					$rel_menu->begin_menu("crm_employment_rels_" . $employee_oid);
					$rels_count = count($work_relations);
					$time = time();
					$active = false;

					foreach ($work_relations as $work_relation_oid)
					{
						$rel = new object($work_relation_oid);
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

					$rel_menu->add_item(array(
						"text" => t("Lisa"),
						"link" => html::get_new_url(
							crm_person_work_relation_obj::CLID,
							$employee_oid,
							array(
								"return_url" => get_ru(),
								"employee" => $employee_oid,
								"employer" => $organization_oid
							)
						)
					));

					$work_relation_menu = html::span(array("content" => $rel_menu->get_menu() . "({$rels_count})", "nowrap" => "1"));

					// enter data
					$age = "";
					if($employee->prop("birth_date"))
					{
						$age = (int)((time() - $employee->prop("birth_date")) / (3600*24*366));
					}
					$employees_table->define_data(array(
						"age" => $age,
						"id" => $employee_oid,
						"sex" => $employee->prop("gender") ? $gender_options[$employee->prop("gender")] : "",
						"name" => $employee->prop_str("name"),
						"firstname" => $employee->prop_str("firstname"),
						"lastname" => $employee->prop_str("lastname"),
//						"email" => $employee->get_mail_tag($organization_oid, $section),
						"email" => join(", " ,$employee->emails()->names()),
						"phone" => join(", " ,$employee->phones()->names()),
						"address" => $employee->get_address_string(),
						"image" => $employee->get_image_tag(), //TODO: pildi link, millele klikkides avaneb t88taja pilt
						"cutcopied" => in_array($employee_oid, $clipboard) ? self::CUTCOPIED_COLOUR : "",
						"legend" => $active ? "lightgreen" : "silver",
						"work_relation" => $work_relation_menu
					));
				}
			}
			else
			{
				$r = class_base::PROP_IGNORE;
			}
		}
		catch (awex_param_type_employee_search $e)
		{
			$ui_msg = $e->default_human_readable_error_message;

			if (!$ui_msg)
			{
				throw $e;
			}

			class_base::show_error_text(t("Viga otsinguparameetrites. {$ui_msg}"));
		}

		return $r;
	}

	private function define_employees_table($employees_table)
	{
		// awcb_object_selection
		$fields_data = array(
			array(
				"name" => "cal",
				"chgbgcolor" => "cutcopied",
				"caption" => t("&nbsp;"),
				"width" => "1"
			),
			array(
				"name" => "image",
				"caption" => t("&nbsp;"),
				"chgbgcolor" => "cutcopied",
				"align" => "center",
				"width" => "1"
			),
			array(
				"name" => "name",
				"caption" => t("Nimi"),
				"sortable" => "1",
				"chgbgcolor" => "cutcopied",
				"callback" => array($this, "get_person_name_field"),
				"callb_pass_row" => true
			),
			array(
				"name" => "sex",
				"caption" => t("Sugu"),
				"sortable" => "1",
				"chgbgcolor" => "cutcopied",
			),
			array(
				"name" => "age",
				"caption" => t("Vanus"),
				"sortable" => "1",
				"chgbgcolor" => "cutcopied",
			),
			array(
				"name" => "address",
				"caption" => t("Aadress"),
				"sortable" => "1",
				"chgbgcolor" => "cutcopied",
			),
			array(
				"name" => "phone",
				"chgbgcolor" => "cutcopied",
				"caption" => t("Telefon"),
				"sortable" => "1"
			),
			array(
				"name" => "email",
				"chgbgcolor" => "cutcopied",
				"caption" => t("E-post"),
				"sortable" => "1"
			),
			array(
				"name" => "work_relation",
				"chgbgcolor" => "legend",
				"caption" => t("T&ouml;&ouml;suhe")
			)
		);

		foreach($fields_data as $field_data)
		{
			$employees_table->define_field($field_data);
		}

		$employees_table->define_chooser(array(
			"name"=>"check",
			"width" => "1",
			"field"=>"id"
		));
		$employees_table->set_default_sortby("name");
	}

	// vcl table callback method
	public function get_person_name_field($arr)
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
			array("return_url" => get_ru()),
			parse_obj_name($name)
		);
	}

	public function _get_organizational_units_table(&$arr)
	{
		$r = class_base::PROP_IGNORE;
		$organization_o = $arr["obj_inst"];
		$organizational_units_table = $arr["prop"]["vcl_inst"];
		$this->define_organizational_units_table($organizational_units_table);

		if ($this->selected_object and !$this->selected_object->is_a(crm_profession_obj::CLID))
		{
			$list = $organization_o->get_sections($this->selected_object);
			if($list->count())
			{
				$r = class_base::PROP_OK;
				$section = $list->begin();
				$url = $this->req->get_uri();
				$this->clear_search_args($url);
				$clipboard = (array) aw_session::get(self::CLIPBOARD_DATA_VAR);

				do
				{
					$section_oid = $section->id();

					// get actions menu
					$menu = new popup_menu();
					$menu->begin_menu("crm_employees_management_units_menu_{$section_oid}");
					$menu->add_item(array(
						"text" => t("Muuda"),
						"link" => html::get_change_url($section_oid, array(
							"return_url" => get_ru()
						))
					));

					// compose order editor textbox
					$order_edit = html::textbox(array(
						"name" => "organizational_units_table[{$section_oid}][ord]",
						"size" => "2",
						"value" => $section->ord()
					));

					// get name/link
					$url->set_arg(self::REQVAR_NODE, $section_oid);
					$name = html::href(array("url" => $url->get(), "caption" => $section->prop_str("name")));
					$members = $section->get_workers();
					$m3 = array();
					$x = 0;
					foreach($members->arr() as $member)
					{
						if($members->count() - 3 < $x)
						{
							$m3[]= $member->name();
						}
						$x++;
					}
					// enter data
					$organizational_units_table->define_data(array(
						"oid" => $section_oid,
						"cutcopied" => in_array($section_oid, $clipboard) ? self::CUTCOPIED_COLOUR : "",
						"menu" => $menu->get_menu(),
						"name" => $name,
						"ord" => $order_edit,
						"icon" => html::img(array("url" => icons::get_icon_url(crm_section_obj::CLID))),
						
						"editor" => $section->modifiedby(),
						"members" => $members->count(),
							"last_members" => join(", " , $m3)
					));
				}
				while ($section = $list->next());
			}
		}

		return $r;
	}
 
	public function _set_add_existing_profession_oid($arr)
	{
	//	arr($arr); die();
		$r = class_base::PROP_IGNORE;

		if (empty($arr["prop"]["value"]) or empty($arr["request"][self::REQVAR_NODE]))
		{
			return $r;
		}

		// load person and profession objects
		try
		{
			$prof = obj($arr["prop"]["value"], array(), crm_profession_obj::CLID);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Amet pole loetav"));
			return $r;
		}

		// popup search found id, parent is set
		$employer = $arr["obj_inst"];

		$section = empty($arr["request"]["es_c"]) ? null : obj($arr["request"]["es_c"], array(), CL_CRM_SECTION);
		$profession = $employer->add_profession($section);
		$profession->set_name($prof->name());
		$profession->save();
		$params = array();
		if (isset($arr["return_url"])) $params["return_url"] = $arr["return_url"];
		if (isset($arr["save_autoreturn"])) $params["save_autoreturn"] = $arr["save_autoreturn"];

		$r = html::get_change_url($profession->id(), $params);
		return $r;
	}


	public function _set_add_existing_employee_oid($arr)
	{
		$r = class_base::PROP_IGNORE;

		if (empty($arr["prop"]["value"]) or empty($arr["request"][self::REQVAR_NODE]))
		{
			// nothing requested
			return $r;
		}

		// load person and profession objects
		try
		{
			$employee = obj($arr["prop"]["value"], array(), crm_person_obj::CLID);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("T&ouml;&ouml;taja pole loetav"));
			return $r;
		}

		try
		{
			$profession = obj($arr["request"][self::REQVAR_NODE], array(), crm_profession_obj::CLID);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Amet pole loetav"));
			return $r;
		}

		// popup search found id, parent is set
		$employer = $arr["obj_inst"];

		// add employee to this company
		$employer->add_employee($profession, $employee);

		return $r;
	}

	public function _set_organizational_units_table(&$arr)
	{
		$r = class_base::PROP_OK;
		$organization_o = $arr["obj_inst"];
		$error_oids = array();

		if (isset($arr["request"]["organizational_units_table"]) and is_array($arr["request"]["organizational_units_table"]))
		{
			foreach ($arr["request"]["organizational_units_table"] as $section_oid => $data)
			{
				if ($section_oid)
				{
					try
					{
						$section = obj($section_oid, array(), crm_section_obj::CLID);

						//set order
						if (isset($data["ord"]) and $data["ord"] != $section->ord())
						{
							$section->set_ord((int) $data["ord"]);
							$section->save();
						}
					}
					catch (Exception $e)
					{
						$error_oids[] = $section_oid;
					}
				}
			}

			if ($error_oids)
			{
				$this->show_error_text(sprintf(t("Viga &uuml;ksuste (objektid %s) salvestamisel"), implode(", ", $error_oids)));
			}
		}

		return $r;
	}

	private function define_organizational_units_table($organizational_units_table)
	{

		$organizational_units_table->define_field(array(
			"width" => "1",
			"name" => "icon"
		));
		$organizational_units_table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => "1",
			"chgbgcolor" => "cutcopied"
		));
		$organizational_units_table->define_field(array(
			"name" => "editor",
			"caption" => t("Viimane muutja"),
			"sortable" => "1",
			"chgbgcolor" => "cutcopied"
		));
		$organizational_units_table->define_field(array(
			"name" => "members",
			"caption" => t("Liikmeid üksuses"),
			"sortable" => "1",
			"chgbgcolor" => "cutcopied"
		));

		$organizational_units_table->define_field(array(
			"name" => "last_members",
			"caption" => t("Viimati lisatud"),
			"sortable" => "1",
			"chgbgcolor" => "cutcopied"
		));

		$organizational_units_table->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"sortable" => "1",
			"chgbgcolor" => "cutcopied"
		));

		$organizational_units_table->define_chooser(array(
			"field" => "oid",
			"width" => "1",
			"name" => "check"
		));
		$organizational_units_table->define_field(array(
			"width" => "1",
			"name" => "menu"
		));


	}

	public function _get_professions_table(&$arr)
	{
		$r = class_base::PROP_IGNORE;
		$organization_o = $arr["obj_inst"];
		$professions_table = $arr["prop"]["vcl_inst"];
		$this->define_professions_table($professions_table);

		if ($this->selected_object and !$this->selected_object->is_a(crm_profession_obj::CLID))
		{
			$list = $organization_o->get_professions($this->selected_object);
			if($list->count())
			{
				$r = class_base::PROP_OK;
				$profession = $list->begin();
				$url = $this->req->get_uri();
				$this->clear_search_args($url);
				$clipboard = (array) aw_session::get(self::CLIPBOARD_DATA_VAR);

				do
				{
					$profession_oid = $profession->id();

					// get actions menu
					$menu = new popup_menu();
					$menu->begin_menu("crm_employees_management_professions_menu_{$profession_oid}");
					$menu->add_item(array(
						"text" => t("Muuda"),
						"link" => html::get_change_url($profession_oid, array("return_url" => get_ru()))
					));

					// compose order editor textbox
					$order_edit = html::textbox(array(
						"name" => "professions_table[{$profession_oid}][ord]",
						"size" => "2",
						"value" => $profession->ord()
					));

					// get name/link
					$url->set_arg(self::REQVAR_NODE, $profession_oid);
					$name = html::href(array("url" => $url->get(), "caption" => $profession->prop_str("name")));

					// enter data
					$professions_table->define_data(array(
						"oid" => $profession_oid,
						"menu" => $menu->get_menu(),
						"cutcopied" => in_array($profession_oid, $clipboard) ? self::CUTCOPIED_COLOUR : "",
						"name" => $name,
						"ord" => $order_edit,
					));
				}
				while ($profession = $list->next());
			}
		}

		return $r;
	}

	public function _set_professions_table(&$arr)
	{
		$r = class_base::PROP_OK;
		$organization_o = $arr["obj_inst"];
		$error_oids = array();

		if (isset($arr["request"]["professions_table"]) and is_array($arr["request"]["professions_table"]))
		{
			foreach ($arr["request"]["professions_table"] as $profession_oid => $data)
			{
				if ($profession_oid)
				{
					try
					{
						$profession = obj($profession_oid, array(), crm_profession_obj::CLID);

						//set order
						if (isset($data["ord"]) and $data["ord"] != $profession->ord())
						{
							$profession->set_ord((int) $data["ord"]);
							$profession->save();
						}
					}
					catch (Exception $e)
					{
						$error_oids[] = $profession_oid;
					}
				}
			}

			if ($error_oids)
			{
				$this->show_error_text(sprintf(t("Viga ametite (objektid %s) salvestamisel"), implode(", ", $error_oids)));
			}
		}

		return $r;
	}

	private function define_professions_table($professions_table)
	{
		$professions_table->define_field(array(
			"width" => "1",
			"name" => "menu"
		));

		$professions_table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => "1",
			"chgbgcolor" => "cutcopied"
		));

		$professions_table->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"sortable" => "1",
			"chgbgcolor" => "cutcopied"
		));

		$professions_table->define_chooser(array(
			"field" => "oid",
			"width" => "1%",
			"name" => "check"
		));
	}

	private function clear_search_args(aw_uri $uri)
	{
		foreach ($this->search_args as $name)
		{
			$uri->unset_arg($name);
		}
	}

	/**
		@attrib name=cut
		@param check required type=array
		@param es_c required type=oid acl=view
		@param post_ru required type=string
	**/
	public function cut($arr)
	{
		aw_session::set(self::CLIPBOARD_DATA_VAR, $arr["check"]);
		aw_session::set(self::CLIPBOARD_FROM_CAT_VAR, $arr["es_c"]);
		aw_session::set(self::CLIPBOARD_ACTION_VAR, "cut");
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste
		@param id required type=oid acl=view
		@param es_c required type=oid acl=view
		@param post_ru required type=string
	**/
	public function paste($arr)
	{
		$r = $arr["post_ru"];

		try
		{
			$employer = obj($arr["id"], array(), crm_company_obj::CLID); // id parameter is pre-checked by orb
		}
		catch (awex_obj $e)
		{
			$this->show_error_text(t("Viga organisatsiooniobjekti laadimisel."));
			return $r;
		}

		// find old and new parent objects (from where and to where to paste)
		try
		{
			$old_parent = new object(aw_session::get(self::CLIPBOARD_FROM_CAT_VAR));
			$new_parent = new object($arr["es_c"]);
		}
		catch (awex_obj $e)
		{
			$this->show_error_text(t("Viga kleepimiskoha objekti laadimisel."));
			return $r;
		}

		// process cut/copied objects
		$clipboard = aw_session::get(self::CLIPBOARD_DATA_VAR);
		$errors = $paste_errors = $load_errors = array();

		foreach ($clipboard as $oid)
		{
			try
			{
				$o = new object($oid);

				if ($o->is_a(crm_person_obj::CLID))
				{
					if (!$old_parent->is_a(crm_profession_obj::CLID))
					{ // unknown error source
						$errors[t("T&ouml;&ouml;taja varasem amet pole loetav")] = "";
					}
					elseif (!$new_parent->is_a(crm_profession_obj::CLID))
					{ // trying to paste not under a profession
						$errors[t("T&ouml;&ouml;tajat saab kleepida vaid ameti valiku all")] = "";
					}
					else
					{ // all parameters correct
						// end active work rel(s). in profession where cut action requested
						$old_rels = crm_person_work_relation_obj::find($o, $old_parent, $employer);
						if($old_rels->count())
						{
							$old_work_relation = $old_rels->begin();

							do
							{
								$employer->finish_work_relation($old_work_relation);
							}
							while ($old_work_relation = $old_rels->next());
						}

						// create new work rel.
						$employer->add_employee($new_parent, $o);
					}
				}
				elseif ($o->is_a(crm_section_obj::CLID))
				{
					// check parent -- is org or section
					if (!$new_parent->is_a(crm_section_obj::CLID) and !$new_parent->is_a(crm_company_obj::CLID))
					{ // trying to paste under a profession
						$errors[t("&Uuml;ksust saab kleepida vaid teise &uuml;ksuse v&otilde;i organisatsiooni alla")] = "";
					}
					else
					{
						// set new parent
						$new_parent_oid = $new_parent->is_a(crm_company_obj::CLID) ? 0 : $new_parent->id();
						$o->set_prop("parent_section", $new_parent_oid);
						$o->save();
					}
				}
				elseif ($o->is_a(crm_profession_obj::CLID))
				{
					// check parent -- is org or section
					if (!$new_parent->is_a(crm_section_obj::CLID) and !$new_parent->is_a(crm_company_obj::CLID))
					{ // trying to paste under a profession
						$errors[t("Ametit saab kleepida vaid &uuml;ksuse v&otilde;i organisatsiooni alla")] = "";
					}
					else
					{
						// set new parent
						$new_parent_oid = $new_parent->is_a(crm_company_obj::CLID) ? 0 : $new_parent->id();
						$o->set_prop("parent_section", $new_parent_oid);
						$o->save();
					}
				}
			}
			catch (awex_obj $e)
			{
				$load_errors[] = $oid;
			}
			catch (Exception $e)
			{
				$paste_errors[] = $oid;
			}
		}

		if ($load_errors)
		{
			$this->show_error_text(sprintf(t("Viga kleebitava(te) objekti(de) (%s) laadimisel"), implode(", ", $load_errors)));
		}

		if ($paste_errors)
		{
			$this->show_error_text(sprintf(t("Viga objekti(de) (%s) kleepimisel"), implode(", ", $paste_errors)));
		}

		if ($errors)
		{
			foreach ($errors as $message => $null)
			{
				$this->show_error_text($message);
			}
		}

		// clear clipboard
		aw_session::del(self::CLIPBOARD_DATA_VAR);
		aw_session::del(self::CLIPBOARD_FROM_CAT_VAR);
		aw_session::del(self::CLIPBOARD_ACTION_VAR);

		// exit
		return $r;
	}
}
