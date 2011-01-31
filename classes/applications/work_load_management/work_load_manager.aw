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

@groupinfo declarations caption="Täidetud deklaratsioonid"
@default group=declarations

	@property declarations_toolbar type=toolbar no_caption=1 store=no

	@property declarations type=table no_caption=1 store=no

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

	public function _get_declarations_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
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

		$t->add_fields(array(
			"name" => t("Nimi")
		));

		$entries = $arr["obj_inst"]->get_entries();
		foreach($entries->arr() as $entry)
		{
			$t->define_data(array(
				"name" => $entry->val("name"),
			));
		}
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
