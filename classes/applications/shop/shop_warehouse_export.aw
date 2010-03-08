<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_warehouse_export.aw,v 1.7 2008/01/31 13:50:07 kristo Exp $
// shop_warehouse_export.aw - Lao v&auml;ljaminek 
/*

@classinfo syslog_type=ST_SHOP_WAREHOUSE_EXPORT relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property confirm type=checkbox ch_value=1 field=meta method=serialize
@caption Kinnita

@groupinfo export caption="V&auml;ljamineku sisu"

@property export group=export field=meta method=serialize type=table no_caption=1

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT,CL_SHOP_PACKET
@caption v&auml;lja l&auml;inud toode

@reltype ORDER value=2 clid=CL_SHOP_ORDER
@caption tellimus

*/

class shop_warehouse_export extends class_base
{
	function shop_warehouse_export()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_export",
			"clid" => CL_SHOP_WAREHOUSE_EXPORT
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "export":
				$this->do_exp_table($arr);
				break;

			case "confirm":
				if ($arr["obj_inst"]->prop("confirm") == 1)
				{
					// can't unconfirm after confirmation
					return PROP_IGNORE;
				}
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "export":
				$this->save_exp_table($arr);
				break;

			case "confirm":
				if ($arr["obj_inst"]->prop("confirm") != 1 && $data["value"] == 1)
				{
					// confirm was clicked, do the actual add
					$this->do_confirm($arr["obj_inst"]);
				}
				break;
		}
		return $retval;
	}	

	function _init_exp_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));

		$t->define_field(array(
			"name" => "count",
			"caption" => t("Mitu l&auml;ks"),
			"align" => "center"
		));
	}

	function do_exp_table(&$arr)
	{
		$pd = $arr["obj_inst"]->meta("exp_content");

		$this->_init_exp_table($arr["prop"]["vcl_inst"]);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PRODUCT")) as $c)
		{
			if ($arr["obj_inst"]->prop("confirm") == 1)
			{
				$cnt = $pd[$c->prop("to")];
			}
			else
			{
				$cnt = html::textbox(array(
					"name" => "pd[".$c->prop("to")."]",
					"value" => $pd[$c->prop("to")],
					"size" => 5
				));
			}
			$arr["prop"]["vcl_inst"]->define_data(array(
				"name" => $c->prop("to.name"),
				"count" => $cnt
			));
		}
	}

	function save_exp_table(&$arr)
	{
		$arr["obj_inst"]->set_meta("exp_content", $arr["request"]["pd"]);
	}

	function do_confirm($o)
	{
		if ($o->prop("confirm") == 1)
		{
			// make sure we don't re-confirm receptions
			return;
		}
		$inf = new aw_array($o->meta("exp_content"));
		foreach($inf->get() as $id => $prod)
		{
			$to = obj($id);
			$prod = new aw_array($prod);
			if ($to->is_property("item_count"))
			{
				$item_count = $to->prop("item_count");
				foreach($prod->get() as $x => $val)
				{
					$item_count -= is_numeric($val) ? $val : $val["items"];
				}
				$to->set_prop("item_count", $item_count);
				$to->save();
			}
		}
		$o->set_prop("confirm", 1);
		$o->save();
	}
}
?>
