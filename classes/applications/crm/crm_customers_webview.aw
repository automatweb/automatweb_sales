<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_customers_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_customers_webview
@default group=general

	@property mode type=chooser orient=vertical field=aw_mode
	@caption Kuvamisviis

	@property clids type=chooser orient=vertical multiple=1 field=aw_clids
	@caption Kuvatavad klassid

*/

class crm_customers_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_customers_webview",
			"clid" => crm_customers_webview_obj::CLID
		));
	}

	public function _get_clids(&$arr)
	{
		$arr["prop"]["options"] = array(
			crm_company_obj::CLID => obj(null, array(), crm_company_obj::CLID)->class_title(),
			crm_person_obj::CLID => obj(null, array(), crm_person_obj::CLID)->class_title(),
		);
	}

	public function _get_mode(&$arr)
	{
		$arr["prop"]["options"] = array(
			crm_customers_webview_obj::MODE_USER_COMPANY_CUSTOMERS => t("Kuva sisseloginud kasutaja organisatsiooni kliente"),
		);

		if (!isset($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] = crm_customers_webview_obj::MODE_USER_COMPANY_CUSTOMERS;
		}

		return PROP_OK;
	}

	/**
		@attrib name=show params=name
		@param id required type=int
		@param charset optional type=string
	**/
	public function show($arr)
	{
		$webview = obj($arr["id"], array(), crm_customers_webview_obj::CLID);

		$this->read_template("show.tpl");
		
		$this->vars(array(
			"webview.id" => $webview->id(),
		));

		switch ($webview->prop("mode"))
		{
			case crm_customers_webview_obj::MODE_USER_COMPANY_CUSTOMERS:
				$this->vars(array(
					"webview.company" => user::get_current_company(),
				));
		}

		$customers = $webview->get_customers();
		$CUSTOMER = "";
		if ($customers->count() > 0)
		{
			$customer = $customers->begin();
			do
			{
				$this->__parse_customer($customer);
				$CUSTOMER .= $this->parse("CUSTOMER");
			} while ($customer = $customers->next());
		}

		$this->vars_safe(array(
			"CUSTOMER" => $CUSTOMER,
		));

		$html = $this->parse();

		if (!empty($arr["charset"]))
		{
			$html = iconv(aw_global_get("charset"), $arr["charset"], $html);
		}

		return $html;
	}

	//	TODO: Generalize this!
	protected function __parse_customer($customer)
	{
		$this->vars(array(
			"id" => $customer->id(),
			"name" => $customer->name()
		));
		if ($customer->is_a(crm_person_obj::CLID))
		{
			$this->vars(array(
				"gender" => $customer->prop("gender"),
				"gender.str" => $customer->prop_str("gender"),
				"birthday" => $customer->prop("birthday"),
			));
		}
		elseif ($customer->is_a(crm_company_obj::CLID))
		{
			$this->vars(array(
				"title" => $customer->get_title(),
			));
		}
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_customers_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_customers_webview` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_mode" === $field or "aw_clids" === $field)
			{
				$this->db_add_col("aw_crm_customers_webview", array(
					"name" => $field,
					"type" => "tinyint"
				));
				$r = true;
			}
		}

		return $r;
	}
}
