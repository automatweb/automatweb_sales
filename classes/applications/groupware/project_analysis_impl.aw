<?php
/*
@classinfo maintainer=markop
*/
class project_analysis_impl extends class_base
{
	function project_analysis_impl()
	{
		$this->init();
	}

	function _get_analysis_tb($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Anal&uuml;&uuml;si t&ouml;&ouml;laud"),
			"url" => html::get_new_url(CL_PROJECT_ANALYSIS_WS, $arr["obj_inst"]->id(), array("return_url" => get_ru(), "alias_to" => $arr["obj_inst"]->id(), "reltype" => 23))
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));
	}

	function _init_analysis_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Hindamislaua nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Hindamislaua looja nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _get_analysis_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_analysis_table($t);

		$u = get_instance(CL_USER);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ANALYSIS_WS")) as $c)
		{
			$st = $c->to();
			$p = $u->get_person_for_uid($st->createdby());
			$t->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"createdby" => $p->name(),
				"created" => $st->created(),
				"ord" => $st->ord(),
				"oid" => $c->prop("to")
			));
		}
	}
}

?>
