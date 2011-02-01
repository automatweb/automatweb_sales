<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_work_load_manager master_index=brother_of master_table=objects index=aw_oid

@default table=aw_work_load_manager
@default group=general

@groupinfo settings caption="Seaded"

	@groupinfo professions caption="Ametikohad" parent=settings
	@default group=professions

		@property professions_toolbar type=toolbar no_caption=1 store=no

		@property professions type=table no_caption=1 store=no

	@groupinfo competences caption="Kompetentsid" parent=settings
	@default group=competences

		@property competences_toolbar type=toolbar no_caption=1 store=no

		@property competences type=table no_caption=1 store=no

	@groupinfo research_groups caption="Uurimisrühmad" parent=settings
	@default group=research_groups

		@property research_groups_toolbar type=toolbar no_caption=1 store=no

		@property research_groups type=table no_caption=1 store=no

	@groupinfo publications caption="Publikatsioonid" parent=settings
	@default group=publications

		@property publications_toolbar type=toolbar no_caption=1 store=no

		@property publication_categories type=table no_caption=1 store=no

	@groupinfo rates caption="Arvestusmäärad" parent=settings
	@default group=rates

		@property rates_toolbar type=toolbar no_caption=1 store=no

		@property rates type=table no_caption=1 store=no

@groupinfo declarations caption="Deklaratsioonid" submit=no submit_method=get
@default group=declarations

	@property declarations_toolbar type=toolbar no_caption=1 store=no

	@layout declarations_split type=hbox width=25%:75%

		@layout declarations_left type=vbox parent=declarations_split

			@layout declarations_filter type=vbox parent=declarations_left area_caption=Deklaratsioonide&nbsp;filter

				@property declarations_filter type=treeview parent=declarations_filter store=no no_caption=1

			@layout declarations_search type=vbox parent=declarations_left area_caption=Deklaratsioonide&nbsp;otsing

				@property decs_name type=textbox store=no captionside=top parent=declarations_search
				@caption Nimi

				@property decs_profession type=textbox store=no captionside=top parent=declarations_search
				@caption Ametikoht

				@property decs_unit type=textbox store=no captionside=top parent=declarations_search
				@caption Üksus

				@property decs_submit type=submit store=no no_caption=1 parent=declarations_search
				@caption Otsi
		
		@layout declarations_right type=vbox parent=declarations_split

			@layout declarations_charts type=hbox width=50%:50% parent=declarations_right

				@layout declarations_chart_professions type=vbox area_caption=Ametikohtade&nbsp;osakaalud parent=declarations_charts
					
					@property chart_professions type=google_chart parent=declarations_chart_professions no_caption=1 store=no

				@layout declarations_chart_competences type=vbox area_caption=Kompetentside&nbsp;jaotus parent=declarations_charts
					
					@property chart_competences type=google_chart parent=declarations_chart_competences no_caption=1 store=no

			@property declarations type=table no_caption=1 store=no parent=declarations_right

*/

class work_load_manager extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/work_load_management/work_load_manager",
			"clid" => CL_WORK_LOAD_MANAGER
		));
	}

	public function _get_decs_name($arr)
	{
		if(automatweb::$request->arg_isset("decs_name"))
		{
			$arr["prop"]["value"] = automatweb::$request->arg("decs_name");
		}
		return PROP_OK;
	}

	public function _get_decs_profession($arr)
	{
		if(automatweb::$request->arg_isset("decs_profession"))
		{
			$arr["prop"]["value"] = automatweb::$request->arg("decs_profession");
		}
		return PROP_OK;
	}

	public function _get_decs_unit($arr)
	{
		if(automatweb::$request->arg_isset("decs_unit"))
		{
			$arr["prop"]["value"] = automatweb::$request->arg("decs_unit");
		}
		return PROP_OK;
	}

	public function _get_declarations_filter($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$url = new aw_uri(get_ru());
		$url->unset_arg("decf_competence");
		
		$t->add_item (0, array (
			"name" => t("Kõik kompetentsid"),
			"id" => "competences",
			"parent" => 0,
			"url" => $url->get()
		));
		foreach($arr["obj_inst"]->get_competences()->names() as $id => $name)
		{
			$url->set_arg("decf_competence", $id);
			$t->add_item ("competences", array(
				"name" => $name,
				"id" => "competence_".$id,
				"parent" => 0,
				"url" => $url->get()
			));
		}

		if(automatweb::$request->arg_isset("decf_competence"))
		{
			$t->set_selected_item("competence_".automatweb::$request->arg("decf_competence"));
		}
		else
		{
			$t->set_selected_item("competences");
		}

		return PROP_OK;
	}

	public function _get_chart_professions($arr)
	{
		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE_3D);
		$c->set_size(array(
			"width" => 400,
			"height" => 100,
		));
		$c->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_SOLID,
			"colors" => array(
				"color" => "e9e9e9",
			),
		));
		$data = array();
		$labels = array();
		foreach($arr["obj_inst"]->get_entries()->arr() as $entry)
		{
			if(!is_array($entry->val("professions")) or !$this->declaration_filter($entry))
			{
				continue;
			}

			foreach($entry->val("professions") as $id => $profession)
			{
				if(!empty($profession["active"]))
				{
					if(!isset($data[$id]))
					{
						$data[$id] = $profession["load"];
						$labels[$id] = obj($id)->name();
					}
					else
					{
						$data[$id] += $profession["load"];
					}
				}
			}
		}
		$c->add_data($data);
		$c->set_labels($labels);
		$c->set_title(array(
			"text" => t("Ametikohtade osakaalud kogukoormuste kaupa"),
			"color" => "666666",
			"size" => 11,
		));
	}

	public function _get_chart_competences($arr)
	{
		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array(
			"width" => 400,
			"height" => 100,
		));
		$c->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_SOLID,
			"colors" => array(
				"color" => "e9e9e9",
			),
		));
		$data = array();
		$labels = array();
		foreach($arr["obj_inst"]->get_entries()->arr() as $entry)
		{
			if(!is_array($entry->val("competences")) or !$this->declaration_filter($entry))
			{
				continue;
			}

			foreach($entry->val("competences") as $id => $competence)
			{
				if(!empty($competence["active"]))
				{
					if(!isset($data[$id]))
					{
						$data[$id] = 1;
						$labels[$id] = obj($id)->name();
					}
					else
					{
						$data[$id]++;
					}
				}
			}
		}
		$c->add_data($data);
		$c->set_labels($labels);
		$c->set_title(array(
			"text" => t("Akadeemiliste kompetentside osakaalud deklaratsioonides"),
			"color" => "666666",
			"size" => 11,
		));
	}

	public function _get_declarations_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$ol = new object_list(array(
			"class_id" => CL_WORK_LOAD_DECLARATION,
			"manager" => $arr["obj_inst"]->id,
		));
		$t->add_button(array(
			"name" => "new",
			"caption" => t("Sisesta uus deklaratsioon"),
			"img" => "new.gif",
			"url" => html::get_change_url($ol->begin(), array("entry_id" => "new", "group" => "work_loads")),
		));
		$t->add_delete_button();
	}

	public function _get_professions_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_STUDY_ORGANISATION_PROFESSION), $arr["obj_inst"]->id);
		$t->add_save_button();
		$t->add_delete_button();
	}

	public function _get_rates_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_STUDY_ORGANISATION_RATE), $arr["obj_inst"]->id);
		$t->add_save_button();
		$t->add_delete_button();
	}

	public function _get_competences_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_STUDY_ORGANISATION_COMPETENCE), $arr["obj_inst"]->id);
		$t->add_save_button();
		$t->add_delete_button();
	}

	public function _get_research_groups_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_STUDY_ORGANISATION_RESEARCH_GROUP), $arr["obj_inst"]->id);
		$t->add_save_button();
		$t->add_delete_button();
	}

	public function _get_publications_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_STUDY_ORGANISATION_PUBLICATION_CATEGORY), $arr["obj_inst"]->id);
		$t->add_save_button();
		$t->add_delete_button();
	}

	public function _get_professions($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Ametikohad"));
		$t->define_chooser();

		$t->set_default("callback", array($this, "professions_textbox"));
		$t->set_default("callb_pass_row", true);
		$t->set_default("align", "center");

		$t->add_fields(array(
			"ord" => t("Jrk"),
			"name" => t("Ametikoht"),
			"teaching" => t("&Otilde;ppetöö (min)"),
			"research" => t("Teadustöö (min)"),
			"administrating" => t("Administratiivtöö"),
			"competence" => t("N&otilde;utav kompetents"),
			"load" => t("N&otilde;utav töömaht")
		));
		$t->define_field(array(
			"name" => "rated",
			"caption" => t("Kuva arvestusmäärade tabelis"),
			"callback" => array($this, "professions_checkbox")
		));

		$ol = $arr["obj_inst"]->get_professions();

		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id,
				"ord" => $o->ord(),
				"name" => $o->name,
				"teaching" => $o->teaching,
				"research" => $o->research,
				"administrating" => $o->administrating,
				"competence" => $o->competence,
				"load" => $o->load,
				"rated" => $o->rated,
			));
		}
	}

	public function professions_textbox($row)
	{
		return html::textbox(array(
			"name" => "professions[{$row["oid"]}][{$row["_this_cell"]}]",
			"value" => $row[$row["_this_cell"]],
			"size" => $row["_this_cell"] == "name" ? NULL : 4,
			"post_append_text" => $row["_this_cell"] == "name" ? html::href(array(
				"url" => html::get_change_url($row["oid"]),
				"caption" => html::img(array(
					"url" => "images/icons/edit.gif",
					"alt" => t("Muuda"),
					"title" => t("Muuda"),
				))
			)) : NULL,
		));
	}

	public function professions_checkbox($row)
	{
		return html::checkbox(array(
			"name" => "professions[{$row["oid"]}][{$row["_this_cell"]}]",
			"checked" => $row[$row["_this_cell"]]
		));
	}

	public function _get_competences($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Akadeemilised kompetentsid"));
		$t->define_chooser();

		$t->set_default("callback", array($this, "competences_textbox"));
		$t->set_default("callb_pass_row", true);
		$t->set_default("align", "center");

		$t->add_fields(array(
			"ord" => t("Jrk"),
			"name" => t("Kompetents"),
			"value" => t("Punktiline väärtus"),
		));
		$t->define_field(array(
			"name" => "rated",
			"caption" => t("Kuva arvestusmäärade tabelis"),
			"callback" => array($this, "competences_checkbox")
		));

		$ol = $arr["obj_inst"]->get_competences();

		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id,
				"ord" => $o->ord(),
				"name" => $o->name,
				"value" => $o->value,
				"rated" => $o->rated,
			));
		}
	}

	public function competences_textbox($row)
	{
		return html::textbox(array(
			"name" => "competences[{$row["oid"]}][{$row["_this_cell"]}]",
			"value" => $row[$row["_this_cell"]],
			"size" => $row["_this_cell"] == "name" ? NULL : 4,
			"post_append_text" => $row["_this_cell"] == "name" ? html::href(array(
				"url" => html::get_change_url($row["oid"]),
				"caption" => html::img(array(
					"url" => "images/icons/edit.gif",
					"alt" => t("Muuda"),
					"title" => t("Muuda"),
				))
			)) : NULL,
		));
	}

	public function competences_checkbox($row)
	{
		return html::checkbox(array(
			"name" => "competences[{$row["oid"]}][{$row["_this_cell"]}]",
			"checked" => $row[$row["_this_cell"]]
		));
	}

	public function _get_research_groups($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Uurimisrühmad, grandid"));
		$t->define_chooser();

		$t->set_default("callback", array($this, "research_groups_textbox"));
		$t->set_default("callb_pass_row", true);
		$t->set_default("align", "center");

		$t->add_fields(array(
			"ord" => t("Jrk"),
			"name" => t("Kompetents"),
		));

		$ol = $arr["obj_inst"]->get_research_groups();

		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id,
				"ord" => $o->ord(),
				"name" => $o->name,
			));
		}
	}

	public function research_groups_textbox($row)
	{
		return html::textbox(array(
			"name" => "research_groups[{$row["oid"]}][{$row["_this_cell"]}]",
			"value" => $row[$row["_this_cell"]],
			"size" => $row["_this_cell"] == "name" ? NULL : 4,
			"post_append_text" => $row["_this_cell"] == "name" ? html::href(array(
				"url" => html::get_change_url($row["oid"]),
				"caption" => html::img(array(
					"url" => "images/icons/edit.gif",
					"alt" => t("Muuda"),
					"title" => t("Muuda"),
				))
			)) : NULL,
		));
	}

	public function _get_publication_categories($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Publikatsioonide kategooriad"));
		$t->define_chooser();

		$t->set_default("callback", array($this, "publication_categories_textbox"));
		$t->set_default("callb_pass_row", true);
		$t->set_default("align", "center");

		$t->add_fields(array(
			"ord" => t("Jrk"),
			"name" => t("Publikatsiooni kategooria"),
		));

		$ol = $arr["obj_inst"]->get_publication_categories();

		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id,
				"ord" => $o->ord(),
				"name" => $o->name,
			));
		}
	}

	public function publication_categories_textbox($row)
	{
		return html::textbox(array(
			"name" => "publication_categories[{$row["oid"]}][{$row["_this_cell"]}]",
			"value" => $row[$row["_this_cell"]],
			"size" => $row["_this_cell"] == "name" ? NULL : 4,
			"post_append_text" => $row["_this_cell"] == "name" ? html::href(array(
				"url" => html::get_change_url($row["oid"]),
				"caption" => html::img(array(
					"url" => "images/icons/edit.gif",
					"alt" => t("Muuda"),
					"title" => t("Muuda"),
				))
			)) : NULL,
		));
	}

	public function _get_rates($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Kehtestatud arvestusmäärad"));
		$t->define_chooser();

		$t->set_default("callback", array($this, "rates_textbox"));
		$t->set_default("callb_pass_row", true);
		$t->set_default("align", "center");

		$t->add_fields(array(
			"ord" => t("Jrk"),
			"name" => t("Arvestusmäära nimetus"),
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Tüüp"),
			"callback" => array($this, "rates_chooser")
		));
		$t->define_field(array(
			"name" => "category",
			"caption" => t("Kategooria"),
			"callback" => array($this, "categories_chooser")
		));

		$applicables = $arr["obj_inst"]->get_rate_applicables();

		foreach($applicables->names() as $oid => $name)
		{
			$t->define_field(array(
				"name" => "applicable_$oid",
				"caption" => $name
			));
		}

		$rates = $arr["obj_inst"]->get_rates();

		foreach($rates->arr() as $oid => $o)
		{
			$t->define_data(array(
				"oid" => $oid,
				"ord" => $o->ord,
				"name" => $o->name,
				"type" => $o->type,
				"category" => $o->category,
				"applicables" => $o->applicables
			));
		}
	}

	public function rates_textbox($row)
	{
		if(substr($row["_this_cell"], 0, 11) === "applicable_")
		{
			$applicable_id = substr($row["_this_cell"], 11);
			$name = "rates[{$row["oid"]}][applicables][{$applicable_id}]";
			$value = $row["applicables"][$applicable_id];
		}
		else
		{
			$name = "rates[{$row["oid"]}][{$row["_this_cell"]}]";
			$value = $row[$row["_this_cell"]];
		}

		return html::textbox(array(
			"name" => $name,
			"value" => $value,
			"size" => $row["_this_cell"] == "name" ? NULL : 4,
			"post_append_text" => $row["_this_cell"] == "name" ? html::href(array(
				"url" => html::get_change_url($row["oid"]),
				"caption" => html::img(array(
					"url" => "images/icons/edit.gif",
					"alt" => t("Muuda"),
					"title" => t("Muuda"),
				))
			)) : NULL,
		));
	}

	public function categories_chooser($row)
	{
		$rate = new study_organisation_rate();

		return html::select(array(
			"name" => "rates[{$row["oid"]}][category]",
			"value" => $row[$row["_this_cell"]],
			"options" => $rate->category_options,
		));
	}

	public function rates_chooser($row)
	{
		$rate = new study_organisation_rate();

		return html::select(array(
			"name" => "rates[{$row["oid"]}][type]",
			"value" => $row[$row["_this_cell"]],
			"options" => $rate->type_options,
		));
	}

	public function _get_declarations($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser();
		$t->set_default("sortable", true);
		$t->add_fields(array(
			"name" => t("Nimi"),
			"profession" => t("Ametikoht"),
			"unit" => t("Üksus"),
			"competences" => t("Akadeemiline kompetents"),
		));

		$entries = $arr["obj_inst"]->get_entries();
		
		$ol = new object_list(array(
			"class_id" => CL_WORK_LOAD_DECLARATION,
			"manager" => $arr["obj_inst"]->id,
		));
		$declaration = $ol->begin();
		$competence_names = $arr["obj_inst"]->get_competences()->names();

		foreach($entries->arr() as $entry)
		{
			if(!$this->declaration_filter($entry))
			{
				continue;
			}

			$competences = array();
			if(is_array($entry->val("competences")))
			{
				foreach($entry->val("competences") as $competence_id => $competence_data)
				{
					if(!empty($competence_data["active"]))
					{
						$competences[] = $competence_names[$competence_id];
					}
				}
			}

			$t->define_data(array(
				"oid" => $entry->id,
				"name" => html::obj_change_url($declaration, $entry->val("name"), array("entry_id" => $entry->id, "group" => "work_loads")),
				"profession" => $entry->val("profession"),
				"unit" => $entry->val("unit"),
				"competences" => implode(", ", $competences),
			));
		}
	}

	private function declaration_filter($entry)
	{
		if(strlen(automatweb::$request->arg_isset("decs_name")) > 0 && !stristr($entry->val("name"), automatweb::$request->arg("decs_name")))
		{
			return false;
		}
		if(strlen(automatweb::$request->arg("decs_profession")) > 0 && !stristr($entry->val("profession"), automatweb::$request->arg("decs_profession")))
		{
			return false;
		}
		if(strlen(automatweb::$request->arg("decs_unit")) > 0 && !stristr($entry->val("unit"), automatweb::$request->arg("decs_unit")))
		{
			return false;
		}
		if(automatweb::$request->arg_isset("decf_competence") > 0 && (!is_array($entry->val("competences")) || !in_array(automatweb::$request->arg("decf_competence"), array_keys($entry->val("competences")))))
		{
			return false;
		}

		return true;
	}

	public function callback_post_save($arr)
	{
		$data = array();
		foreach(array("professions", "competences", "research_groups", "publication_categories", "rates") as $data_key)
		{
			$data = $data + (isset($arr["request"][$data_key]) ? $arr["request"][$data_key] : array());
		}

		foreach($data as $oid => $props)
		{
			$o = obj($oid);
			foreach($props as $key => $value)
			{
				$o->set_prop($key, $value);
			}
			$o->save();
		}
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_work_load_manager" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_work_load_manager` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_work_load_manager", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
