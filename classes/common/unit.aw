<?php
/*
@classinfo syslog_type=ST_UNIT relationmgr=yes no_comment=1 prop_cb=1

@default table=objects
@default field=meta
@default method=serialize

@default group=general
	@property unit_name_morphology_spec type=textbox
	@comment Reeglid &uuml;hiku nime kasutamiseks koos numbriga. N&auml;ide: 1 tund; * tundi
	@caption &Uuml;hiku morfoloogia

	@property unit_code type=textbox
	@caption &Uuml;hiku t&auml;his

	@property unit_sort type=select
	@comment Suurus, mida &uuml;hik m&otilde;&otilde;dab
	@caption Suurus


@groupinfo units caption=K&otilde;ik&nbsp;&uuml;hikud
@default group=units
	@property units_tlb type=toolbar store=no no_caption=1

	@property units_tbl type=table store=no no_caption=1


@groupinfo transl caption=T&otilde;lgi
@default group=transl
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

*/

class unit extends class_base
{
	protected $trans_props = array(
		"name", "unit_name_morphology_spec", "unit_code"
	);

	function unit()
	{
		$this->init(array(
			"tpldir" => "common/unit",
			"clid" => CL_UNIT
		));
	}

	public function _get_units_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_save_button();

		return PROP_OK;
	}

	public function _get_units_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(true);

		$t->set_default("sortable", true);
		$t->add_fields(array(
			"name" => t("Nimi"),
			"code" => t("T&auml;his"),
			"sort" => t("Suurus"),
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Aktiivne?"),
			"width" => 200,
			"align" => "center",
			"callback" => array($this, "callback_units_tbl_state"),
			"callb_pass_row" => true,
		));

		$quantity_names = unit_obj::quantity_names();
		foreach(unit_obj::get_all_units()->arr() as $unit)
		{
			$t->define_data(array(
				"id" => $unit->id(),
				"name" => html::obj_change_url($unit),
				"code" => $unit->prop("unit_code"),
				"sort" => isset($quantity_names[$unit->prop("unit_sort")]) ? $quantity_names[$unit->prop("unit_sort")] : t("M&auml;&auml;ramata"),
				"state" => $unit->status(),
			));
		}

		$t->set_default_sortby("name");

		return PROP_OK;
	}

	public function callback_units_tbl_state($row)
	{
		return html::checkbox(array(
			"name" => "units[{$row["id"]}][active]",
			"checked" => $row["state"] == object::STAT_ACTIVE,
		)).html::hidden(array(
			"name" => "units[{$row["id"]}][was_active]",
			"value" => $row["state"] == object::STAT_ACTIVE ? 1 : 0,
		));
	}

	function _get_unit_sort(&$arr)
	{
		$retval = class_base::PROP_OK;
		$arr["prop"]["options"] = array(0 => "") + unit_obj::quantity_names();
		return $retval;
	}

	public function callback_post_save($arr)
	{
		if (automatweb::$request->arg_isset("units") and is_array($units = automatweb::$request->arg("units")))
		{
			foreach($units as $id => $unit)
			{
				if ($unit["was_active"] and empty($unit["active"]) or !$unit["was_active"] and !empty($unit["active"]))
				{
					$unit_obj = new object($id, array(), CL_UNIT);
					$unit_obj->set_prop("status", empty($unit["active"]) ? object::STAT_NOTACTIVE : object::STAT_ACTIVE);
					$unit_obj->save();
				}
			}
		}
	}

	function get_unit_list($choose = null)
	{
		$ol = unit_obj::get_all_units();
		if($choose)
		{
			return array(0=>t("--vali--")) + $ol->names();
		}
		return $ol->names();
	}
}
