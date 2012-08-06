<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_companies_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_companies_webview
@default table=objects
@default field=meta
@default method=serialize

@default group=general

@property companies type=relpicker multiple=1 store=connect reltype=RELTYPE_COMPANY
@caption Ettev&otilde;tted

@property groups type=select multiple=1
@caption Kliendigrupid

@property relation_direction type=select
@caption Suhte suund

@property relation_status type=select
@caption Suhte staatus

@property customer_manager type=select multiple=1
@caption Kliendihaldur

@property template type=select
@caption Seotud organisatsioonide veebis kuvamise kujundusp&otilde;hi

@groupinfo jrk caption="J&auml;rjesta"
@default group=jrk

	@property result_table type=table store=no no_caption=1

	@property order_by type=select
	@caption J&auml;rjestamise eelistus

@groupinfo urls caption="Viita toorelt"
@default group=urls

	@property urls_table type=table store=no no_caption=1

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Ettev&otilde;te


*/

class crm_companies_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_companies_webview",
			"clid" => crm_companies_webview_obj::CLID
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "groups":
			case "relation_direction":
			case "customer_manager":
			case "template":
			case "relation_status":
				if(!$arr["obj_inst"]->get_companies()->count())
			{
				return PROP_IGNORE;
			}
		}

		switch($prop["name"])
		{
			case "groups":
				$prop["options"] = array();
				foreach($arr["obj_inst"]->get_companies()->arr() as $co)
				{
					foreach($co->get_customer_categories()->names() as $id => $name)
					{
						$prop["options"][$id] = $name;
					}
				}
				break;
			case "relation_direction":
				$prop["options"] = array(
					"ainult ostjad",
					"ainult m&uuml;&uuml;jad",
					"m&auml;lemad"
				);
				break;
			case "relation_status":
				$prop["options"] = array("")+crm_company_customer_data_obj::sales_state_names();
				break;
			case "customer_manager":
				$prop["options"] = $arr["obj_inst"]->get_managers()->names();
				break;
			case "template":
				if(!file_exists (aw_ini_get("site_tpldir")."applications/crm/crm_companies_webview"))
				{
					$prop["type"] = "text";
					$prop["value"] = t("Ei ole &uuml;htegi templeiti")."  ".aw_ini_get("site_tpldir")."applications/crm/crm_companies_webview";
				}
				elseif ($handle = opendir(aw_ini_get("site_tpldir")."applications/crm/crm_companies_webview")) {
					while (false !== ($entry = readdir($handle))) {
						if($entry !== '.' && $entry !== '..') {
							$entry_str = str_replace("_" , " " , $entry);
							$entry_str = str_replace(".tpl" , "" , $entry_str);
							$prop["options"][$entry] = $entry_str;
						}
					}
				}
				break;
			case "order_by":
				$prop["options"] = array(
					"alfabeetiline",
					"uuemad enne",
					"vanemad enne",
					"m&auml;&auml;ra ise"
				);
			break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr['prop'];
	
		switch($prop["name"])
		{
			case "result_table":
				$arr["obj_inst"]->set_meta("orders" , $arr["request"]["ord"]);
				break;
			case "urls_table":
				$arr["obj_inst"]->set_meta("mod_url" , $arr["request"]["mod_url"]);
				break;
		}

		return class_base::PROP_OK;
	}

	function _get_result_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "leader",
			"caption" => t("Juhi nimi"),
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
		));
		$t->define_field(array(
			"name" => "client_manager",
			"caption" => t("Kliendihaldur"),
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
		));

		$customers = $arr["obj_inst"]->get_customers();
		foreach($customers as $cust)
		{
			$cust["ord"] = html::textbox(array(
				"name" => "ord[".$cust["id"]."]",
				"value" => $cust["ord"],
			));

			$t->define_data($cust);
		}
	}

	function _get_urls_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "real_url",
			"caption" => t("Veebiaadress"),
		));
		$t->define_field(array(
			"name" => "mod_url",
			"caption" => t("Veebiaadress"),
		));

		$customers = $arr["obj_inst"]->get_customers();
		foreach($customers as $cust)
		{
			$cust["mod_url"] = html::textbox(array(
				"name" => "mod_url[".$cust["id"]."]",
				"value" => $cust["mod_url"],
			));
			$cust["real_url"] = $cust["real_url"] ? html::href(array(
				"url" => $cust["real_url"],
				"caption" => $cust["real_url"],
			)) : "";
			$t->define_data($cust);
		}

	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}
	
	function show($arr)
	{
		$obj = &obj($arr["id"]);

		$this->read_template($obj->prop("template"));

		$customers = $obj->get_customers();

		$customer_list_sub = "";
		$customer_sub = "";
		$cnt = 1;

		$active_customer = 0;

		foreach($customers as $customerid => $data)
		{
			if(!$active_customer) $active_customer = $customerid;
			if(substr_count($data["url"],$_SERVER['REQUEST_URI']))
			{
				$active_customer = $customerid;
			}
		}

		foreach($customers as $customerid => $data)
		{
			if($active_customer == $customerid)
			{
				$data["active"] = "active";
			}
			else $data["active"] = "";

/*------------- avamisajad ------------*/

		$oinst = new openhours();
		$o_sub = "";
		$company = obj($customerid);
		foreach($company->connections_from(array("type" => "RELTYPE_OPENHOURS")) as $c)
		{
			$oh = $c->to();
			$ohdata = $oh->meta('openhours');
			$oh_vars = array(
				"oh_name" => $oh->name(),
			);
			$this->vars($oh_vars);
			
			$oh_rows = "";

			if($ohdata && is_array($ohdata) && sizeof($ohdata))
			{
				foreach($ohdata as $ohrow)
				{
					$ohrow["day_short"] = $oinst->days_short[$ohrow["day1"]];
					$ohrow["day2_short"] = $oinst->days_short[$ohrow["day2"]];
					$this->vars($ohrow);
					$this->vars(array("HAS_DAY2" =>  $ohrow["day2"] ? $this->parse("HAS_DAY2"):""));
					$oh_rows.= $this->parse("OPEN_HOURS_ROW");
				}
			}
			$this->vars(array("OPEN_HOURS_ROW" => $oh_rows));
			$o_sub.= $this->parse("OPEN_HOURS");
		}
		$this->vars(array("OPEN_HOURS" => $o_sub));

			$this->vars_safe($data);
			$customer_list_sub.=$this->parse("CUSTOMER_LIST");
			$customer_sub.=$this->parse("CUSTOMERS");
			$cnt++;
		}

		$this->vars_safe(array(
			"CUSTOMER_LIST" => $customer_list_sub,
			"CUSTOMERS" => $customer_sub
		));

		return $this->parse();
	}
		
	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_companies_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_companies_webview` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_crm_companies_webview", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
