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

	private function add_to_cat_selection($co, &$sel , $o , $level)
	{
		foreach($co->get_customer_categories($o)->arr() as $id => $o)
		{
			$sel[$id] = str_repeat("--" , $level)." ".$o->name();
			$this->add_to_cat_selection($co, $sel , $o , $level+1);
		}
	}

	private function add_to_sec_selection($co, &$sel , $o , $level)
	{
		foreach($co->get_sections($o)->arr() as $id => $o)
		{
			$sel[$id] = str_repeat("--" , $level)." ".$o->name();
			$this->add_to_sec_selection($co, $sel , $o , $level+1);
		}
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
			/*		$opts = $co->get_customer_categories_hierarchy(null,100); 
					var_dump($opts);*/
					foreach($co->get_customer_categories()->arr() as $id => $o)
					{
					//	var_dump($o->properties());
						if(!$o->prop("parent_category"))
						{
							$prop["options"][$id] = $o->name();
							$this->add_to_cat_selection($co, $prop["options"] , $o , 1);
						}
					}
				}
				break;
			case "relation_direction":
				$prop["options"] = array(
					"ainult ostjad",
					"ainult m&uuml;&uuml;jad",
					"m&otilde;lemad"
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
			case "urls_table":
				return PROP_IGNORE;
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
				$show = $arr["obj_inst"]->meta("show");
				foreach($arr["request"]["show"] as $key => $val)
				{
					$show[$key] = $val;
				}
				$arr["obj_inst"]->set_meta("show" , $show);

				$mod_url = $arr["obj_inst"]->meta("mod_url");
				foreach($arr["request"]["mod_url"] as $key => $val)
				{
					$mod_url[$key] = $val;
				}
				$arr["obj_inst"]->set_meta("mod_url" , $mod_url);

				$orders = $arr["obj_inst"]->meta("orders");
				foreach($arr["request"]["ord"] as $key => $val)
				{
					$orders[$key] = $val;
				}
				$arr["obj_inst"]->set_meta("orders" , $orders);

				$dont_show = $arr["obj_inst"]->meta("dont_show");
				foreach($arr["request"]["dont_show"] as $key => $val)
				{
					$dont_show[$key] = $val;
				}
				$arr["obj_inst"]->set_meta("dont_show" , $dont_show);


		//		$arr["obj_inst"]->set_meta("orders" , $arr["request"]["ord"]);
		//		$arr["obj_inst"]->set_meta("show_what" , $arr["request"]["show_what"]);
				$arr["obj_inst"]->set_meta("dont_show" , $arr["request"]["dont_show"]);
//				$arr["obj_inst"]->set_meta("mod_url" , $arr["request"]["mod_url"]);
				
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
			"name" => "dont_show",
			"caption" => t("&Auml;ra n&auml;ita"),
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
			"name" => "show",
			"caption" => t("Kuva"),
		));
		$t->define_field(array(
			"name" => "show_what"
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
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
			$o = obj($cust["id"]);
			if($o->class_id() != crm_company_obj::CLID) continue;
			$cust["name"] = html::obj_change_url($cust["id"],$cust["name"]);
			$cust["ord"] = html::textbox(array(
				"name" => "ord[".$cust["id"]."]",
				"value" => $cust["ord"],
				"size" => 5
			));
			$cust["dont_show"] = html::checkbox(array(
				"name" => "dont_show[".$cust["id"]."]",
				"value" => 1,
				"checked" => $cust["dont_show"],
			));
			if($cust["show"])
			{
				$options = array();
				foreach($o->get_sections()->arr() as $id => $sec)
				{
					if($sec->prop("parent_section")) continue;
					$options[$id] = $sec->name();
					$this->add_to_sec_selection($o, $options , $sec , 1);
				}

				$cust["show_what"] = html::select(array(
					"name" => "show_what[".$cust["id"]."]",
					"value" => $cust["show_what"],
					"options" => $options,
					"multiple" => 1
				));
			}

			$cust["show"] = html::select(array(
				"name" => "show[".$cust["id"]."]",
				"value" => $cust["show"],
				"options" => array(t("Organisatsiooni") , t("&Uuml;ksuseid"))
			));
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

/*	function _get_urls_table($arr)
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
*/

	function callback_mod_tab($arr)
	{
		if($arr["id"] === "urls")
		{
			return false;
		}
		return true;
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}
	
	function show($arr)
	{
		$obj = obj($arr["id"], null, crm_companies_webview_obj::CLID);

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
			if($data["show"] || $data["dont_show"]) //n2itab yksusi
			{
				continue;
			}
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
			"CUSTOMERS" => $customer_sub,
			"name" => $obj->name()
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
