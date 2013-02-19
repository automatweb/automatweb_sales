<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_order_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_order_webview
@default group=general

@property buyer_type type=chooser multiple=1 orient=vertical table=objects field=meta method=serialize
@caption Kuvatakse

@property order_states_displayed type=chooser multiple=1 orient=vertical table=objects field=meta method=serialize
@caption Kuvatavad staatused

@property states_displayed type=chooser multiple=1 orient=vertical table=objects field=meta method=serialize
@caption Kuvatavad tootmise staatused

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
	
	function _get_order_states_displayed(&$arr)
	{
		$arr["prop"]["options"] = array();
		foreach(mrp_case_obj::get_order_state_names() as $value => $caption)
		{
			$arr["prop"]["options"][$value] = $caption;
		}
		return PROP_OK;	
	}
	
	function _get_states_displayed(&$arr)
	{
		$arr["prop"]["options"] = array();
		foreach(mrp_case_obj::get_state_names() as $value => $caption)
		{
			$arr["prop"]["options"][$value] = $caption;
		}
		return PROP_OK;	
	}
	
	/**
		@attrib name=confirm_order nologin=true
	**/
	function confirm_order($arr)
	{
		$order = obj($arr["order"], null, mrp_case_obj::CLID);
		$order->confirm();
		
		return $arr["return_url"];
	}
	
	/**
		@attrib name=cancel_order nologin=true
	**/
	function cancel_order($arr)
	{
		$order = obj($arr["order"], null, mrp_case_obj::CLID);
		$order->cancel();
		
		return $arr["return_url"];
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
				"order.state" => $order->prop("state"),
				"order.state.name" => mrp_case_obj::get_state_names($order->prop("state")),
				"order.order_state" => $order->prop("order_state"),
				"order.order_state.name" => mrp_case_obj::get_order_state_names($order->prop("order_state")),
				"order.total" => "TODO",
				"order.created" => $order->prop("created"),
				"order.pdf_url" => $order->get_order_pdf()->get_url(),
				"order.pdf_filename" => $order->get_order_pdf()->name(),
				"order.confirm_url" => $this->__get_confirm_url($order),
				"order.cancel_url" => $this->__get_cancel_url($order),
			));
			$ORDER .= $this->parse("ORDER");
		}
		$this->vars_safe(array(
			"ORDER" => $ORDER,
		));
		
		return $this->parse();
	}
	
	private function __get_confirm_url($order)
	{
		return $this->__get_action_url("confirm_order", $order);
	}
	
	private function __get_cancel_url($order)
	{
		return $this->__get_action_url("cancel_order", $order);
	}
	
	private function __get_action_url($action, $order)
	{
		$url = new aw_uri();
		$url->set_arg("class", "order_webview");
		$url->set_arg("action", $action);
		$url->set_arg("order", $order->id);
		$url->set_arg("return_url", automatweb::$request->get_uri());
		
		return $url->get();
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
