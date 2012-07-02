<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_warehouse_item master_index=brother_of master_table=objects index=aw_oid
@extends common/product/aw_product

@default table=aw_shop_warehouse_item
@default group=general

@groupinfo purveyance caption=Tarneinfo
@default group=purveyance

	@property purveyance_tlb type=toolbar store=no no_caption=1

	@property purveyance_tbl type=table store=no no_caption=1

@reltype WAREHOUSE value=25 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class shop_warehouse_item extends aw_product
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_item",
			"clid" => shop_warehouse_item_obj::CLID
		));
	}

	function _get_status_edit(&$arr)
	{
		$arr["prop"]["options"] = array(
			object::STAT_ACTIVE => t("Aktiivne"),
			object::STAT_NOTACTIVE => t("Deaktiivne"),
		);
		$arr["prop"]["value"] = $arr["obj_inst"]->status();

		return PROP_OK;
	}

	function _get_purveyance_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_save_button();
		$t->add_delete_button();
	}

	function _get_purveyance_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Tarnefirma"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "weekdays",
			"caption" => t("Tarnepäevad"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Tarneaeg"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "days",
			"caption" => t("Tarneaeg p&auml;evades"),
			"align" => "center",
		));

		$t->set_caption(t("Tarneaegade muutmise tabel"));
		$t->set_sortable(false);

		$wh = $arr["obj_inst"]->get_current_warehouse();

		$ol = $arr["obj_inst"]->get_purveyances($wh ? $wh->id() : null);
		foreach($ol->arr() as $oid => $o)
		{
			$t->define_data(array(
				"oid" => $oid,
				"name" => html::obj_change_url($o),
				"company" => objpicker::create(array(
					"name" => "purveyances[{$oid}][company]",
					"clid" => crm_company_obj::CLID,
					"object" => $o,
					"value" => $o->prop("company"),
				)),
				"weekdays" => weekdays::create(array(
					"name" => "purveyances[{$oid}][weekdays]",
					"start_from_monday" => true,
					"multiple" => true,
					"value" => $o->prop("weekdays"),
				)),
				"time" => timepicker::create(array(
					"name" => "purveyances[{$oid}][time_from]",
					"value" => $o->prop("time_from"),
				))." - ".timepicker::create(array(
					"name" => "purveyances[{$oid}][time_to]",
					"value" => $o->prop("time_to"),
				)),
				"days" => html::textbox(array(
					"name" => "purveyances[{$oid}][days]",
					"value" => $o->prop("days"),
					"size" => 5,
				)),
			));
		}
		$default_purveyor = $wh ? $wh->get_conf("owner") : 0;
		$t->define_data(array(
			"name" => t("Uus tarnetingimus"),
			"company" => objpicker::create(array(
				"name" => "purveyances[new][company]",
				"clid" => crm_company_obj::CLID,
				"object" => obj(null, array(), CL_SHOP_PRODUCT_PURVEYANCE),
				"value" => $default_purveyor,
			)),
			"weekdays" => weekdays::create(array(
				"name" => "purveyances[new][weekdays]",
				"start_from_monday" => true,
				"multiple" => true,
			)),
			"time" => timepicker::create(array(
				"name" => "purveyances[new][time_from]",
			))." - ".timepicker::create(array(
				"name" => "purveyances[new][time_to]",
			)),
			"days" => html::textbox(array(
				"name" => "purveyances[new][days]",
				"value" => 0,
				"size" => 5,
			)),
		));
	}

	function _set_purveyance_tbl($arr)
	{
		$wh = $arr["obj_inst"]->get_current_warehouse();

		foreach($arr["request"]["purveyances"] as $oid => $data)
		{
			if($oid === "new" and $data["company"])
			{
				$o = obj(null, array(), CL_SHOP_PRODUCT_PURVEYANCE);
				$o->set_name(sprintf("%s tarnetingimus", $arr["obj_inst"]->name()));
				$o->set_parent($arr["obj_inst"]->id());
				$o->set_prop("object", $arr["obj_inst"]->id());
				$o->set_prop("warehouse", $wh ? $wh->id() : null);
			}
			elseif($this->can("view", $oid))
			{
				$o = obj($oid);
			}
			else
			{
				continue;
			}

			$o->set_prop("company", $data["company"]);
			if (isset($data["weekdays"]))
			{
				$o->set_prop("weekdays", weekdays::days2int($data["weekdays"]));
			}
			$o->set_prop("time_from", timepicker::get_timestamp($data["time_from"]));
			$o->set_prop("time_to", timepicker::get_timestamp($data["time_to"]));
			$o->set_prop("days", $data["days"]);
			$o->save();
		}
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_shop_warehouse_item" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_shop_warehouse_item` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
		}

		return $r;
	}

	// Override parent to avoid multiple calls in message handling
	public function setup_description_document($arr) {}
}
