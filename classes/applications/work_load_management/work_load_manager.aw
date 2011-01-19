<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_work_load_manager master_index=brother_of master_table=objects index=aw_oid

@default table=aw_work_load_manager
@default group=general

@groupinfo settings caption="Seaded"
@default group=settings

	@property toolbar type=toolbar no_caption=1 store=no

	@property professions type=table no_caption=1 store=no

	@property competences type=table no_caption=1 store=no

	@property research_groups type=table no_caption=1 store=no

	@property publication_categories type=table no_caption=1 store=no

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

	public function _get_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(
			CL_STUDY_ORGANISATION_PROFESSION,
			CL_STUDY_ORGANISATION_COMPETENCE,
			CL_STUDY_ORGANISATION_RESEARCH_GROUP,
			CL_STUDY_ORGANISATION_PUBLICATION_CATEGORY
		), $arr["obj_inst"]->id);
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
				"load" => $o->load
			));
		}
	}

	public function professions_textbox($row)
	{
		return html::textbox(array(
			"name" => "professions[{$row["oid"]}][{$row["_this_cell"]}]",
			"value" => $row[$row["_this_cell"]],
			"size" => $row["_this_cell"] == "name" ? NULL : 4,
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

		$ol = $arr["obj_inst"]->get_competences();

		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o->id,
				"ord" => $o->ord(),
				"name" => $o->name,
				"value" => $o->value,
			));
		}
	}

	public function competences_textbox($row)
	{
		return html::textbox(array(
			"name" => "competences[{$row["oid"]}][{$row["_this_cell"]}]",
			"value" => $row[$row["_this_cell"]],
			"size" => $row["_this_cell"] == "name" ? NULL : 4,
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
		));
	}

	public function callback_post_save($arr)
	{
		$data = array();
		foreach(array("professions", "competences", "research_groups", "publication_categories") as $data_key)
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
