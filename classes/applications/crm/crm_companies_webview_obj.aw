<?php

class crm_companies_webview_obj extends _int_object
{
	const CLID = 1817;

	public function get_companies()
	{
		$ret = new object_list();
		foreach($this->connections_from(array("clid" => "CL_CRM_COMPANY")) as $c)
		{
			$ret->add($c->to());
		}
		return $ret;
	}

	public function get_managers()
	{
		$filter = array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA
		);
		switch($this->prop("relation_direction")){
			case "1":
				$filter["buyer"] = $this->get_companies()->ids();
				break;
			case "2":
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"conditions" => array(
								"buyer" =>  $this->get_companies()->ids()
							)
						)),
						new object_list_filter(array(
							"conditions" => array(
								"seller" => $this->get_companies()->ids()
							)
						))
					)
				));

				break;
			default:
				$filter["seller"] = $this->get_companies()->ids();
				break;
		}
		$ol = new object_list($filter);
		$ret = new object_list();
		foreach($ol->arr() as $rel)
		{
			$ret->add($rel->prop("client_manager"));
		}
		return $ret;
	}

	public function get_customers()
	{
		$filter = array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA
		);
		$companies = $this->get_companies()->ids();

		switch($this->prop("relation_direction")){
			case "1":
				$filter["buyer"] = $companies;
				break;
			case "2":
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"conditions" => array(
								"buyer" =>  $companies
							)
						)),
						new object_list_filter(array(
							"conditions" => array(
								"seller" => $companies
							)
						))
					)
				));

				break;
			default:
				$filter["seller"] = $companies;
				break;
		}

		if($this->prop("relation_status"))
		{
			$filter["sales_state"] = $this->prop("relation_status");
		}
		if($this->prop("customer_manager") && is_array($this->prop("customer_manager")) && sizeof($this->prop("customer_manager")))
		{
			$filter["client_manager"] = $this->prop("customer_manager");
		}

		if($this->prop("groups") && is_array($this->prop("groups")) && sizeof($this->prop("groups")))
		{
			$filter["CL_CRM_COMPANY_CUSTOMER_DATA.RELTYPE_CATEGORY"] = $this->prop("groups");
		}

//var_dump($filter);
		$ol = new object_list($filter);

		$ret = array();
		$orders = $this->meta("orders");
		if(empty($orders)) $orders = array(0);
		$mod_url = $this->meta("mod_url");
		foreach($ol->arr() as $o)
		{
			$start = time();
			switch($this->prop("relation_direction")){
				case "1":
					$cust = $o->prop("seller");
						$start = $o->prop("buyer_contract_date");
					break;
				case "2":
					if(in_array($o->prop("seller") , $companies))
					{
						$cust = $o->prop("buyer");
						$start = $o->prop("cust_contract_date");
					}
					else
					{
						$cust = $o->prop("seller");
						$start = $o->prop("buyer_contract_date");
					}
				break;
				default:
					$cust = $o->prop("buyer");
					$start = $o->prop("cust_contract_date");
				break;
			}
			$cust_obj = obj($cust);
			$ret[$cust] = array(
				"id" => $cust,
				"name" => $cust_obj->name(),
				"phone" => join("," , $cust_obj->get_phones()),
				"leader" => $cust_obj->class_id() == CL_CRM_PERSON ? "" : $cust_obj->prop("firmajuht.name"),
				"co_reg" => $cust_obj->class_id() == CL_CRM_PERSON ? "" : $cust_obj->prop("reg_nr"),
				"co_tax" => $cust_obj->class_id() == CL_CRM_PERSON ? "" : $cust_obj->prop("tax_nr"),
				"client_manager" => $o->prop("client_manager.name"),
				"ord" => isset($orders[$cust]) ? $orders[$cust] : max($orders)+100,
				"mod_url" => $mod_url[$cust],
				"real_url" => $cust_obj->get_url(),
				"url" => $mod_url[$cust] ? $mod_url[$cust] : $cust_obj->get_url(),
				"name_small" => str_replace(" ","_",strtolower($cust_obj->name())),
				"short_name" => $cust_obj->class_id() == CL_CRM_PERSON ? $cust_obj->prop("name") : ($cust_obj->prop("short_name") ? $cust_obj->prop("short_name") : $cust_obj->prop("name")),
				"start" => $start,
				"address" => $cust_obj->get_address_string(),
				"open_hours" => $this->get_oh($cust_obj),
				"comment" => $o->comment(),
				"logo" => ""
			);
			$logoo = $cust_obj->get_first_obj_by_reltype("RELTYPE_ORGANISATION_LOGO");
			if ($logoo)
			{
				$img_i = $logoo->instance();
				$ret[$cust]["logo"] = $img_i->make_img_tag_wl($logoo->id());
			}

			$address = $cust_obj->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");
			if($address)
			{
				foreach($address->properties() as $var => $val)
				{
					$ret[$cust]["address_".$var] = $val;
				}

				if($address->prop("coord_x") && $address->prop("coord_y"))
				{
 					$ret[$cust]["google_map_url"] =
'https://maps.google.com/maps?q='.urlencode($address->prop("street")).'+'.$address->prop("house").',+'.urlencode($address->prop("parent.name")).',+Eesti&hl=en&ie=UTF8&ll='.$address->prop("coord_y").','.$address->prop("coord_x").'&spn=0.011141,0.038581&sll=37.0625,-95.677068&sspn=34.450489,79.013672&oq='.urlencode($address->prop("street")).'+'.$address->prop("house").'+p&hnear='.urlencode($address->prop("street")).'+'.$address->prop("house").',+'.urlencode($address->prop("parent.name")).',+80042+'.urlencode($address->prop("parent.parent.name")).',+Estonia&t=m&z=15';					
				}
			}

			$orders[$cust] = $ret[$cust]["ord"];
		}

		uasort($ret, array(&$this , "cmp"));
		return $ret;
	}

	function get_oh($o)
	{
		$oinst = new openhours();
		$ret = "";
		if($o->class_id() == CL_CRM_COMPANY)
		{
			foreach($o->connections_from(array("clid" => "CL_OPENHOURS")) as $c)
			{
				$oh = $c->to();
				$ohdata = $oh->meta('openhours');
				if($ohdata && is_array($ohdata) && sizeof($ohdata))
				{
					foreach($ohdata as $ohrow)
					{
						$ret.= $oinst->days_short[$ohrow["day1"]].($ohrow["day2"] ? "-" .$oinst->days_short[$ohrow["day2"]] : "")." ".$ohrow["h1"].":00... ".$ohrow["h2"].":00".html::linebreak();
					}
				}
			}
		}
		return $ret;
	}

	function cmp($a, $b)
	{
		switch($this->prop("order_by"))
		{
			case "1":
				if ($a["start"] == $b["start"]) {
					return 0;
				}
				return ($a["start"] < $b["start"]) ? -1 : 1;
			case "2":
				if ($a["start"] == $b["start"]) {
					return 0;
				}
				return ($a["start"] > $b["start"]) ? -1 : 1;
			case "3":
				$ord = $this->meta("ord");
				if ($ord[$a["id"]] == $ord[$b["id"]]) {
					return 0;
				}
				return ($ord[$a["id"]] < $ord[$b["id"]]) ? -1 : 1;
			default:
				if ($a["name"] == $b["name"]) {
					return 0;
				}
				return ($a["name"] < $b["name"]) ? -1 : 1;
		}
	}



}

/** Generic crm_companies_webview_obj exception **/
class awex_crm_companies_webview_obj extends awex_obj {}
