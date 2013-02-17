<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_order_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_order_webview
@default group=general

@property buyer_type type=chooser multiple=1 orient=vertical table=objects field=meta method=serialize
@caption Kuvatakse

*/

class order_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/order_management/order_webview",
			"clid" => order_webview_obj::CLID
		));
	}
	
	function _get_buyer_type(&$arr)
	{
		$arr["prop"]["options"] = array(
			crm_person_obj::CLID => t("Isiku tellimusi"),
			crm_company_obj::CLID => t("Organisatsiooni tellimusi"),
		);
		return PROP_OK;
	}
	
	function show($arr)
	{
		$this->read_template("show.tpl");
	
		$o = obj($arr["id"], null, order_webview_obj::CLID);	
		$orders = $o->get_orders();
		
		$ORDER = "";
		foreach($orders->arr() as $order)
		{
			$this->vars(array(
				"order.id" => $order->id(),
				"order.name" => $order->prop("name"),
				"order.state" => mrp_case_obj::get_state_names($order->prop("state")),
				"order.order_state" => mrp_case_obj::get_order_state_names($order->prop("order_state")),
				"order.total" => "TODO",
				"order.created" => $order->prop("created"),
				"order.pdf_url" => $order->get_order_pdf()->get_url(),
			));
			$ORDER .= $this->parse("ORDER");
		}
		$this->vars(array(
			"ORDER" => $ORDER,
		));
		
		return $this->parse();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_order_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_order_webview` (
					`aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY	(`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "INT"
						));
						break;

				}
				$r = true;
			}
		}

		return $r;
	}
}
