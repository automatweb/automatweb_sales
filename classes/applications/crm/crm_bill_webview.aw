<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_bill_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_bill_webview
@default group=general

@property buyer_type type=chooser multiple=1 orient=vertical table=objects field=meta method=serialize
@caption Kuvatakse

@property states_displayed type=chooser multiple=1 orient=vertical table=objects field=meta method=serialize
@caption Kuvatavad staatused

*/

class crm_bill_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_bill_webview",
			"clid" => crm_bill_webview_obj::CLID,
		));
	}
	
	function _get_buyer_type(&$arr)
	{
		$arr["prop"]["options"] = array(
			crm_person_obj::CLID => t("Isiku arveid"),
			crm_company_obj::CLID => t("Organisatsiooni arveid"),
		);
		return PROP_OK;
	}
	
	function _get_states_displayed(&$arr)
	{
		$arr["prop"]["options"] = array();
		foreach(crm_bill_obj::status_names() as $value => $caption)
		{
			$arr["prop"]["options"]["s{$value}"] = $caption;
		}
		return PROP_OK;	
	}
	
	function show($arr)
	{
		$this->read_template("show.tpl");
	
		$o = obj($arr["id"], null, crm_bill_webview_obj::CLID);	
		$bills = $o->get_bills();
		
		$BILL = "";
		foreach($bills->arr() as $bill)
		{
			$this->vars(array(
				"bill.id" => $bill->id(),
				"bill.bill_no" => $bill->prop("bill_no"),
				"bill.bill_date" => $bill->prop("bill_date"),
				"bill.due_date" => mktime(0, 0, 0, date("m", $bill->prop("bill_date")), date("d", $bill->prop("bill_date")) + $bill->prop("bill_due_date_days"), date("Y", $bill->prop("bill_date"))),
				"bill.sum" => is_object($bill->currency()) ? $bill->currency()->sum_with_currency($bill->prop("sum"), 2) : number_format($bill->prop("sum"), 2),
				"bill.state" => crm_bill_obj::status_names($bill->prop("state")),
				"bill.pdf_url" => $bill->get_invoice_pdf()->get_url(),
			));
			$BILL .= $this->parse("BILL");
		}
		$this->vars(array(
			"BILL" => $BILL,
		));
		
		return $this->parse();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_bill_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_bill_webview` (
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
