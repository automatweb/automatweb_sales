<?php

namespace automatweb;
// shop_order_center_filter_entry.aw - Tellimiskeskkonna toodete filter
/*

@classinfo syslog_type=ST_SHOP_ORDER_CENTER_FILTER_ENTRY relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

	@property oc type=relpicker reltype=RELTYPE_ORDER_CENTER field=meta method=serialize
	@caption Tellimiskeskkond

	@property filter_params type=table no_caption=1 store=no
	@caption Filtri parameetrid

@groupinfo filter caption="Koosta filter"
@default group=filter

	@property filter type=table store=no no_caption=1
*/

class shop_order_center_filter_entry extends class_base
{
	const AW_CLID = 1412;

	function shop_order_center_filter_entry()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order_center_filter_entry",
			"clid" => CL_SHOP_ORDER_CENTER_FILTER_ENTRY
		));
	}

	function _get_oc($arr)
	{
		if ($arr["new"] && $arr["request"]["set_oc"])
		{
			$arr["prop"]["value"] = $arr["request"]["set_oc"];
		}

		if ($this->can("view", $arr["prop"]["value"]) && !isset($arr["prop"]["options"][$arr["prop"]["value"]]))
		{
			$arr["prop"]["options"][$arr["prop"]["value"]] = obj($arr["prop"]["value"])->name();
		}
	}

	function _get_filter_params($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_filter_params_table($t, $arr);
	}

	function _init_filter_params_table($t, $arr)
	{
		
	}
	
	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _get_filter($arr)
	{
		// columns are filters
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_filter_table($t, $arr["obj_inst"]->prop("oc"));

		// rows are selectable filter values
		$this->_insert_filter_data($t, $arr["obj_inst"]->prop("oc"), $arr["obj_inst"]);
	}

	function _set_filter($arr)
	{
		$oc = obj($arr["obj_inst"]->prop("oc"));
		foreach($oc->filter_get_fields() as $field_name => $field_caption)
		{
			$values = $oc->filter_get_all_values($field_name);
			$sel_vals = array();
			foreach(safe_array($arr["request"]["sel_filt"][$field_name]) as $val => $one)
			{
				if ($one == 1)
				{
					$sel_vals[$val] = $values[$val];
				}
			}
			$arr["obj_inst"]->filter_set_selected_values($field_name, $sel_vals);
		}
		$arr["obj_inst"]->filter_set_user_captions($arr["request"]["field_caption"]);
		$oc->save();
	}

	function _insert_filter_data($t, $oc, $o)
	{
		$td = array();

		$field_captions = $o->filter_get_user_captions();

		$oc = obj($oc);
		foreach($oc->filter_get_fields() as $field_name => $field_caption)
		{
			$td[$field_name] = t("Tulba nimi")."<br/>".html::textbox(array(
				"name" => "field_caption[$field_name]",
				"value" => $field_captions[$field_name],
			))."<br/>".t("Tulba valikud");
		}
		$t->define_data($td);

		$td = array();
		foreach($oc->filter_get_fields() as $field_name => $field_caption)
		{
			$values = $oc->filter_get_all_values($field_name);
			$sel_values = $o->filter_get_selected_values($field_name);
			$num = 0;
			foreach($values as $value => $value_caption)
			{
				$td[$num++][$field_name] = html::checkbox(array(
					"name" => "sel_filt[$field_name][$value]",
					"value" => 1,
					"checked" => !empty($sel_values[$value])
				))." ".$value_caption;
			}
		}

		foreach($td as $row)
		{
			$t->define_data($row);
		}
		$t->set_sortable(false);
		$t->set_caption(t("Vali aktiivsed filtrite v&auml;&auml;rtused"));
	}

	private function _init_filter_table($t, $oc)
	{
		$oc = obj($oc);
		foreach($oc->filter_get_fields() as $field_name => $field_caption)
		{
			$t->define_field(array(
				"name" => $field_name,
				"caption" => $field_caption,
				"align" => "left"
			));
		}
	}
}

?>
