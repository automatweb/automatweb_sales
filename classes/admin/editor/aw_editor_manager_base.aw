<?php

class aw_editor_manager_base extends aw_template
{
	protected function _init_t(&$t, $tbl_type)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => $tbl_type,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Asukoht"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Vali"),
		));
	}

	protected function _get_searchable_props()
	{
		return array(array(
			"name" => "s[name]",
			"type" => "textbox",
			"caption" => t("Nimi"),
			"value" => $_GET["s"]["name"]
		));
	}

	protected function draw_form($arr)
	{
		classload("cfg/htmlclient");
		$htmlc = new htmlclient(array(
			'template' => "default",
		));
		$htmlc->start_output();

		foreach($this->_get_searchable_props() as $prop)
		{
			$htmlc->add_property($prop);
		}

		$htmlc->add_property(array(
			"name" => "s[submit]",
			"type" => "submit",
			"value" => t("Otsi"),
		));

		$htmlc->add_property(array(
			"name" => "s[my]",
			"type" => "checkbox",
			"caption" => t("Minu lisatud"),
			"value" => $_GET["s"]["my"],
		));

		$htmlc->add_property(array(
			"name" => "s[last]",
			"type" => "checkbox",
			"caption" => t("Viimased 30"),
			"value" => $_GET["s"]["last"],
		));

		$htmlc->finish_output(array(
			"action" => "manager",
			"method" => "GET",
			"data" => array(
				"docid" => $arr["docid"],
				"orb_class" => get_class($this),
				"reforb" => 0
			)
		));

		return $htmlc->get_result();
	}

	protected function gen_location_for_obj($o)
	{
		$o = obj($o->parent());
		for($i=0;$i<3;$i++)
		{
			$ret[] = $o?$o->name():NULL;
			$o = (($o) && $s = $o->parent())?obj($s):false;
		}
		return join(" / ", array_reverse($ret));
	}
}
