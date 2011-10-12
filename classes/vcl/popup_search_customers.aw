<?php

class popup_search_customers extends popup_search
{
	protected $clid = array(crm_company_customer_data_obj::CLID);
	protected $type = "buyer"; // buyer|seller|all
	protected $customer_relation_self_party_oid = 0;
	//TODO: implement. seller ja all

	public function set_class_id($clid)
	{
	}

	public function set_self_oid($customer_relation_self_party_oid)
	{
		$this->customer_relation_self_party_oid = $customer_relation_self_party_oid;
	}

	protected function _get_search_form_property_definitions(array $arr)
	{
		return array(
			"name" => array(
				"name" => "s[name]",
				"type" => "textbox",
				"value" => ifset($arr, "s", "name"),
				"caption" => t("Kliendi nimi")
			),
			"self_oid" => array(
				"name" => "s[self_oid]",
				"type" => "hidden",
				"value" => ifset($arr, "self_oid"),
			)
		);
	}

	function get_popup_url()
	{
		$url = $this->mk_my_orb("do_ajax_search", array(
			"id" => $this->oid,
			"in_popup" => "1",
			"self_oid" => $this->customer_relation_self_party_oid,
			"start_empty" => "1",
			"reload_layout" => isset($this->reload_layouts) ? $this->reload_layouts :"",
			"reload_property" => isset($this->reload_property) ? $this->reload_property :"",
			"reload_window" => !empty($this->reload_window),
			"clid" => $this->clid,
			"action" => $this->action,
			"property" => $this->property
		), get_class($this));
		return $url;
	}

	protected function _get_search_results_fields(array $arr)
	{
		return array(
			"oid" => array(
				"name" => "oid",
				"caption" => t("ID")
			),
			"name" => array(
				"name" => "name",
				"caption" => t("Kliendi nimi")
			),
			"comment" => array(
				"name" => "comment",
				"caption" => t("Kommentaar")
			),
			"modifiedby" => array(
				"name" => "modifiedby",
				"caption" => t("Muutja")
			),
			"modified" => array(
				"name" => "modified",
				"caption" => t("Muudetud"),
				"format" => "d.m.Y H:i",
				"type" => "time"
			)
		);
	}

	protected function _get_search_results_filter(array $arr)
	{
		$filter = array();

		if(!empty($arr["name"]))
		{
			$filter["buyer.name"] = "%".iconv("UTF-8",aw_global_get("charset"),  $arr["name"])."%";
		}

		if(!empty($arr["oid"]))
		{
			$filter["oid"] = $arr["oid"]."%";
		}

		if (count($filter) or empty($arr["start_empty"])) // don't show default search results if start_empty parameter true
		{
			if(!empty($arr["clid"]))
			{
				$filter["class_id"] = $arr["clid"];
			}

			$filter["seller"] = $arr["self_oid"];
			$filter[] = new obj_predicate_limit($this->search_results_limit);
		}

		return $filter;
	}

	protected function _get_search_result_prop_name(object $o, array $arr)
	{
		return $o->prop("buyer.name");
	}

	protected function _get_search_result_prop_comment(object $o, array $arr)
	{
		return $o->comment();
	}
}
